<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BiberLtd\Bundle\CoreBundle\Composer;

use Symfony\Component\ClassLoader\ClassCollectionLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Yaml\Yaml;
use Composer\Script\CommandEvent;
use BiberLtd\Bundle\GitBundle\Services\Git;
use BiberLtd\Bundle\GitBundle\Services\GitRepo;
/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class ScriptHandler
{
    public static function installBiberLtdBundles(CommandEvent $event)
    {
        $rootDir = getcwd();
        $appDir = 'app';
        $kernelFile = $appDir . '/AppKernel.php';

        $fs = new Filesystem();
        if (!$event->getIO()->askConfirmation('Would you like to install any BiberLtd bundles? [Y/n] ', true)) {
            return;
        }
        $bundles = $event->getIO()->ask('Which bundles do you want to install?', null);
        if (is_null($bundles)) {
            $event->getIO()->write('You did not type any bundle');
            return;
        }
        $branch = $event->getIO()->ask('Please type a branch name for git submodules', 'project');
        if ($branch == 'project') {
            $event->getIO()->write('You did not type branch name, \'project\' will be used.');
            return;
        }
        $client = new Git();
        $arr = explode(',', $bundles);
        if (count($arr) > 0) {
            /** read config.yml */
            $configFile = $rootDir . '/app/config/config.yml';
            $configs = Yaml::parse($configFile);
            foreach ($arr as $name) {
                $item = strtolower($name);
                $folder = $rootDir . '/vendor/biberltd/' . $item . '/BiberLtd/Bundle/' . $name;
                $fs->mkdir($folder);
                $event->getIO()->write('Installing biberltd/' . $item . ' bundle ...');
                $folder = $rootDir . '/vendor/biberltd/' . $item . '/BiberLtd/Bundle/' . $name;
                $repo = $client->clone_remote($folder, "https://github.com/biberltd/$item.git");
                $repo = $client->open($rootDir);
                $repo->run('submodule add ' . $folder);
                $repo2 = $client->open($folder);
                $repo2->run('checkout -b ' . $branch);
                $event->getIO()->write("biberltd/$item bundle installed successfully");
                /** APP KERNEL*/
//                $ref = '$bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();';
                $ref = 'return $bundles;';
                $bundleDeclaration = "\$bundles[] = new BiberLtd\\Bundle\\$name\\BiberLtd$name();";
                $content = file_get_contents($kernelFile);
                if (false === strpos($content, $bundleDeclaration)) {
                    $updatedContent = str_replace($ref, $bundleDeclaration . "\n         " . $ref, $content);
                    if ($content === $updatedContent) {
                        throw new \RuntimeException('Unable to patch %s.', $kernelFile);
                    }
                    $fs->dumpFile($kernelFile, $updatedContent);
                }
                /** AUTLOAD NAMESPACES */
                $ref = '\'Assetic\' => array($vendorDir . \'/kriswallsmith/assetic/src\'),';
                $bundleDeclaration = '\'BiberLtd\\\\Bundle\\\\' . $name . '\' => array($vendorDir . \'/biberltd/' . $item . '\'),';
                $autloadFile = $rootDir . '/vendor/composer/autoload_namespaces.php';
                $content = file_get_contents($autloadFile);
                $event->getIO()->write($autloadFile);
                if (false === strpos($content, $bundleDeclaration)) {
                    $updatedContent = str_replace($ref, $bundleDeclaration . "\n    " . $ref, $content);
                    if ($content === $updatedContent) {
                        throw new \RuntimeException('Unable to patch %s.', $kernelFile);
                    }
                    $fs->dumpFile($autloadFile, $updatedContent);
                }
                /** Adding orm records of bundles to config.yml */
                $configs['doctrine']['orm']['entity_managers']['default']['mappings'][$name]['type'] = 'annotation';
                $configs['doctrine']['orm']['entity_managers']['default']['mappings'][$name]['alias'] = $name;
                $configs['doctrine']['orm']['entity_managers']['default']['mappings'][$name]['prefix'] = 'BiberLtd\\Bundle\\' . $name . '\\Entity';
                $configs['doctrine']['orm']['entity_managers']['default']['mappings'][$name]['dir'] = "%kernel.root_dir%/../vendor/biberltd/$item/BiberLtd/Bundle/$name/Entity";
            }
//            unset($configs['doctrine']['orm']['auto_mapping']);
//            if (!in_array('smarty',$configs['framework']['templating']['engines'])) {
//                $configs['framework']['templating']['engines'] = array('twig','smarty');
//            }
//            if (!isset($configs['doctrine']['dbal']['connections']['default'])) {
//                $configs['doctrine']['dbal'] = array();
//                $configs['doctrine']['dbal']['default_connection'] = 'default';
//                $configs['doctrine']['dbal']['connections']['default'] = array();
//                $configs['doctrine']['dbal']['connections']['default']['driver'] = "%database_driver%";
//                $configs['doctrine']['dbal']['connections']['default']['host'] = "%database_host%";
//                $configs['doctrine']['dbal']['connections']['default']['port'] = "%database_port%";
//                $configs['doctrine']['dbal']['connections']['default']['dbname'] = "%database_name%";
//                $configs['doctrine']['dbal']['connections']['default']['user'] = "%database_user%";
//                $configs['doctrine']['dbal']['connections']['default']['password'] = "%database_password%";
//                $configs['doctrine']['dbal']['connections']['default']['charset'] = 'UTF8';
//
//            }
//            if (!isset($configs['doctrine']['orm']['default_entity_manager'])) {
//                $configs['doctrine']['orm']['default_entity_manager'] = 'default';
//            }
//            if (!isset($configs['nelmio_api_doc'])) {
//                #$configs['nelmio_api_doc'] = '~';
//            }
//            if (!isset($configs['smarty']['globals'])) {
//                $configs['smarty']['globals']['doctype'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
//                $configs['smarty']['globals']['ga_tracking'] = 'UA-xxxxx-x';
//                $configs['smarty']['options']['autload_filters']['output'] = array('trimWhiteSpace');
//            }
            file_put_contents($configFile, Yaml::dump($configs, 10));
        }
    }
}
