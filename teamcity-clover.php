<?php

/**
 * Script, which publishes the code coverage clover.xml from PHPUnit to Teamcity
 *
 * @author Michel Hunziker <info@michelhunziker.com>
 * @copyright Copyright (c) 2014 Michel Hunziker <info@michelhunziker.com>
 * @license http://www.opensource.org/licenses/BSD-3-Clause The BSD-3-Clause License
 */


if ($argc != 2) {
    echo 'Path to the clover.xml is required.';
    exit(1);
}

$path = $argv[1];
if (!file_exists($path)) {
    echo 'Path to the clover.xml is incorrect.';
    exit(1);
}


$cloverXml = new SimpleXMLElement($path, null, true);
$metrics = $cloverXml->project->metrics;

if (!$metrics) {
    echo 'clover.xml does not contain code coverage metrics.';
    exit(1);
}

$teamcityXml = file_exists('teamcity-info.xml')
    ? new SimpleXMLElement('teamcity-info.xml', null, true)
    : new SimpleXMLElement('<build />');

$data = array(
    'CodeCoverageAbsLTotal' => (int) $metrics["elements"],
    'CodeCoverageAbsLCovered' => (int) $metrics["coveredelements"],
    'CodeCoverageAbsBTotal' => (int) $metrics["statements"],
    'CodeCoverageAbsBCovered' => (int) $metrics["coveredstatements"],
    'CodeCoverageAbsMTotal' => (int) $metrics["methods"],
    'CodeCoverageAbsMCovered' => (int) $metrics["coveredmethods"],
    'CodeCoverageAbsCTotal' => (int) $metrics["classes"],
    'CodeCoverageB' => $metrics["coveredstatements"] / $metrics["statements"] * 100,
    'CodeCoverageL' => $metrics["coveredelements"] / $metrics["elements"] * 100,
    'CodeCoverageM' => $metrics["coveredmethods"] / $metrics["methods"] * 100,
    'Files' => (int) $metrics["files"],
    'LinesOfCode' => (int) $metrics["loc"],
    'NonCommentLinesOfCode' => (int) $metrics["ncloc"],
);

foreach ($data as $key => $value) {
    $statistic = $teamcityXml->addChild('statisticValue');
    $statistic->addAttribute('key', $key);
    $statistic->addAttribute('value', $value);
}

$success = $teamcityXml->asXML('teamcity-info.xml');
if (!$success) {
    echo 'Could not save teamcity-info.xml';
    exit(1);
} else {
    echo 'clover.xml statistics added to teamcity-info.xml';
    exit(0);
}
