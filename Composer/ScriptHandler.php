<?php
/**
 * @author		Said İmamoğlu
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        10.12.2015
 */
/*
 * Modified from source of Symfont 2:
 *
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

            file_put_contents($configFile, Yaml::dump($configs, 10));
        }
    }
}
