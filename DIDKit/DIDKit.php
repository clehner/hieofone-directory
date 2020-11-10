<?php

namespace DIDKit;

function didkit($cmd, $input=NULL, $allow_failure=FALSE) {
    $proc = proc_open('didkit ' . $cmd, [
        ['pipe', 'r'],
        ['pipe', 'w'],
        ['pipe', 'w']
    ], $pipes);
    if ($proc === FALSE) return 'Unable to run didkit';
    if (fwrite($pipes[0], $input) === FALSE
      || (fclose($pipes[0]) === FALSE)) {
        proc_close($proc);
        throw new Exception('Unable to write to didkit');
    }
    for ($stdout = ''; $str = fread($pipes[1], 4096); $stdout .= $str);
    for ($stderr = ''; $str = fread($pipes[2], 4096); $stderr .= $str);
    $rc = proc_close($proc);
    if ($rc !== 0 && !$allow_failure) {
        throw new Exception("DIDKit Error ($rc)"
        . ($stderr ? ": $stderr" : ''));
    }
    return $stdout;
}

function cli_option($key, $value) {
    switch ($key) {
    case 'challenge': return '--challenge '.escapeshellarg($value);
    case 'created': return '--created '.escapeshellarg($value);
    case 'domain': return '--domain '.escapeshellarg($value);
    case 'proofPurpose': return '--proof-purpose '.escapeshellarg($value);
    case 'verificationMethod': return '--verification-method '.escapeshellarg($value);
    default: throw new Exception('Unknown option \''.$key.'\'');
    }
}

function cli_options($options) {
    $args = '';
    if ($options) foreach ($options as $key => $value) {
        $args .= ' '.cli_option($key, $value);
    }
    return $args;
}

class DIDKit
{
    static function issueCredential($credential, $options, $key_filename) {
        if (!is_string($credential)) $credential = json_encode($credential);
        if (!$key_filename) throw new \TypeError('Missing key filename');
        $cmd = 'vc-issue-credential '.cli_options($options)
            .' --key '.$key_filename;
        return didkit($cmd, $credential);
    }

    static function verifyCredential($credential, $options) {
        if (!is_string($credential)) $credential = json_encode($credential);
        $cmd = 'vc-verify-credential '.cli_options($options);
        return didkit($cmd, $credential, TRUE);
    }

    static function verifyPresentation($presentation, $options) {
        if (!is_string($presentation)) $presentation = json_encode($presentation);
        $cmd = 'vc-verify-presentation '.cli_options($options);
        return didkit($cmd, $presentation, TRUE);
    }
}

class Exception extends \Exception {
}
