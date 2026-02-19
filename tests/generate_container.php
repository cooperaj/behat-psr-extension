#!/usr/bin/env php
<?php

include __DIR__.'/../vendor/autoload.php';

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\XmlDumper;

$extension = new \Acpr\Behat\Psr\ServiceContainer\Extension();
$containerBuilder = new ContainerBuilder();

$extension->load($containerBuilder, ['container' => '', 'application' => '']);

$dumper = new XmlDumper($containerBuilder);

file_put_contents(__DIR__.'/output/container.xml', $dumper->dump());