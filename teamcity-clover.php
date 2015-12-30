<?php

/**
 * Script, which publishes the code coverage clover.xml from PHPUnit to TeamCity.
 *
 * @author Michel Hunziker <info@michelhunziker.com>
 * @copyright Copyright (c) 2015 Michel Hunziker <info@michelhunziker.com>
 * @license http://www.opensource.org/licenses/BSD-3-Clause The BSD-3-Clause License
 */


if ($argc !== 2) {
    echo "Path to the clover.xml is required.\n";
    exit(1);
}

$path = $argv[1];
if (!file_exists($path)) {
    echo "Path to the clover.xml is incorrect.\n";
    exit(1);
}

echo "Parsing clover.xml from: $path\n";
$cloverXml = new SimpleXMLElement($path, null, true);
$metrics = $cloverXml->project->metrics;

if (!$metrics) {
    echo "clover.xml does not contain code coverage metrics.\n";
    exit(1);
}

$coveredClasses = 0;
foreach ($cloverXml->xpath('//class') as $class) {
    if ((int) $class->metrics['coveredmethods'] === (int) $class->metrics['methods']) {
        $coveredClasses++;
    }
}

$data = array(
    'CodeCoverageAbsLTotal' => (int) $metrics['elements'],
    'CodeCoverageAbsLCovered' => (int) $metrics['coveredelements'],
    'CodeCoverageAbsBTotal' => (int) $metrics['statements'],
    'CodeCoverageAbsBCovered' => (int) $metrics['coveredstatements'],
    'CodeCoverageAbsMTotal' => (int) $metrics['methods'],
    'CodeCoverageAbsMCovered' => (int) $metrics['coveredmethods'],
    'CodeCoverageAbsCTotal' => (int) $metrics['classes'],
    'CodeCoverageAbsCCovered' => $coveredClasses,
    'CodeCoverageB' => $metrics['statements'] ? ($metrics['coveredstatements'] / $metrics['statements'] * 100) : 0,
    'CodeCoverageL' => $metrics['elements'] ? ($metrics['coveredelements'] / $metrics['elements'] * 100) : 0,
    'CodeCoverageM' => $metrics['methods'] ? ($metrics['coveredmethods'] / $metrics['methods'] * 100) : 0,
    'CodeCoverageC' => $metrics['classes'] ? ($coveredClasses / $metrics['classes'] * 100) : 0,
    'Files' => (int) $metrics['files'],
    'LinesOfCode' => (int) $metrics['loc'],
    'NonCommentLinesOfCode' => (int) $metrics['ncloc'],
);

foreach ($data as $key => $value) {
    echo "##teamcity[buildStatisticValue key='$key' value='$value']\n";
}

echo "TeamCity has been notified of code coverage metrics.\n";
