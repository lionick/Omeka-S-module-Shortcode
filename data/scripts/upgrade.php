<?php declare(strict_types=1);

namespace Shortcode;

use Omeka\Mvc\Controller\Plugin\Messenger;
use Omeka\Stdlib\Message;

/**
 * @var Module $this
 * @var \Laminas\ServiceManager\ServiceLocatorInterface $services
 * @var string $newVersion
 * @var string $oldVersion
 *
 * @var \Doctrine\DBAL\Connection $connection
 * @var \Doctrine\ORM\EntityManager $entityManager
 * @var \Omeka\Api\Manager $api
 */
// $entityManager = $services->get('Omeka\EntityManager');
$connection = $services->get('Omeka\Connection');
$plugins = $services->get('ControllerPluginManager');
$api = $plugins->get('api');
$settings = $services->get('Omeka\Settings');
// $config = require dirname(__DIR__, 2) . '/config/module.config.php';
$messenger = $services->get('ControllerPluginManager')->get('messenger');

if (version_compare($oldVersion, '3.3.1.2', '<')) {
    require_once __DIR__ . '/upgrade_vocabulary.php';
}

if (version_compare($oldVersion, '3.3.1.6', '<')) {
    require_once __DIR__ . '/upgrade_vocabulary.php';
}

if (version_compare($oldVersion, '3.3.1.7', '<')) {
    $message = new Message(
        'It’s now possible to display the primary image of a resource with shortcut [image].' // @translate
    );
    $messenger->addSuccess($message);
}
