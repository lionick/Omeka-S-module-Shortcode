<?php declare(strict_types=1);

namespace Shortcode;

use Omeka\Module\Exception\ModuleCannotInstallException;
use Omeka\Mvc\Controller\Plugin\Messenger;
use Omeka\Stdlib\Message;

/**
 * @var Module $this
 */

// Update the vocabulary that is used in other modules when present.
$services = $this->getServiceLocator();
$api = $services->get('ControllerPluginManager')->get('api');

// Params to update.
$oldNamespaceUri = 'https://curation.omeka.org/';
$filename = dirname(__DIR__, 2) . '/data/vocabularies/' . 'curation.json';

$vocabularyData = json_decode(file_get_contents($filename), true);
if (!$vocabularyData) {
    throw new ModuleCannotInstallException(
        (string) new Message(
            'Vocabulary data file "%s" not found.', // @translate
            'curation.json'
        ));
}

/** @var \Omeka\Entity\Vocabulary $vocabulary */
$vocabulary = $api->searchOne('vocabularies', ['namespace_uri' => $oldNamespaceUri], ['responseContent' => 'resource'])->getContent();
if (!$vocabulary) {
    return;
}

// Omeka entities are not fluid.
$vocabulary->setNamespaceUri($vocabularyData['vocabulary']['o:namespace_uri']);
$vocabulary->setPrefix($vocabularyData['vocabulary']['o:prefix']);
$vocabulary->setLabel($vocabularyData['vocabulary']['o:label']);
$vocabulary->setComment($vocabularyData['vocabulary']['o:comment']);

$entityManager = $services->get('Omeka\EntityManager');
$entityManager->persist($vocabulary);
$entityManager->flush();

// Upgrade the properties.
/** @var \Omeka\Stdlib\RdfImporter $rdfImporter */
$rdfImporter = $services->get('Omeka\RdfImporter');

try {
    $diff = $rdfImporter->getDiff(
        $vocabularyData['strategy'],
        $vocabulary->getNamespaceUri(),
        [
            'file' => dirname(__DIR__, 2) . '/data/vocabularies/' . $vocabularyData['file'],
            'format' => $vocabularyData['format'],
        ]
    );
} catch (\Omeka\Api\Exception\ValidationException $e) {
    throw new ModuleCannotInstallException(
        (string) new Message(
            'An error occured when updating vocabulary "%s" and the associated properties: %s', // @translate
            $vocabularyData['vocabulary']['o:prefix'],
            $e->getMessage()
        )
    );
}

try {
    $diff = $rdfImporter->update($vocabulary->getId(), $diff);
} catch (\Omeka\Api\Exception\ValidationException $e) {
    throw new ModuleCannotInstallException(
        (string) new Message(
            'An error occured when updating vocabulary "%s" and the associated properties: %s', // @translate
            $vocabularyData['vocabulary']['o:prefix'],
            $e->getMessage()
        )
    );
}

/** @var \Omeka\Entity\Property[] $properties */
$owner = $vocabulary->getOwner();
$properties = $api->search('properties', ['vocabulary_id' => $vocabulary->getId()], ['responseContent' => 'resource'])->getContent();
foreach ($properties as $property) {
    $property->setOwner($owner);
}
$entityManager->flush();

$messenger = new Messenger();
$message = new Message(
    'The vocabulary "%s" was updated successfully.', // @translate
    $vocabularyData['vocabulary']['o:label']
);
$messenger->addSuccess($message);
