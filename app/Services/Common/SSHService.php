<?php

namespace App\Services\Common;

class SSHService {
    public static function addSSHKey(array $domains, $private_key = null): string {
        // Get path to user's .ssh directory
        $ssh_path = getenv('HOME') . '/.ssh';
        $config_file_path = "$ssh_path/config";

        // Ensure the .ssh directory exists
        if (!file_exists($ssh_path)) {
            mkdir($ssh_path, 0700, true);
        }

        // Prepare the private and public key files
        $private_key_path = "$ssh_path/larammerce_auto";
        $public_key_path = "$ssh_path/larammerce_auto.pub";

        // Delete any existing key files
        @unlink($private_key_path);
        @unlink($public_key_path);

        // Run ssh-keygen
        shell_exec("ssh-keygen -t rsa -b 2048 -f $private_key_path -q -N ''");

        // Get the private key
        $private_key = file_get_contents($private_key_path);

        // Get the public key
        $public_key = file_get_contents($public_key_path);

        // Set the private key file permissions to -rw------- (600)
        chmod($private_key_path, 0600);

        // Prepare the config entry
        $config_entry = "";
        foreach ($domains as $domain) {
            $name = str_replace('.', '-', $domain);

            $config_entry .= <<<EOT

Host $name
    Hostname $domain
    IdentityFile $private_key_path
    IdentitiesOnly yes

EOT;
        }

        // If the config file exists, read its content
        if (file_exists($config_file_path)) {
            $config_content = file_get_contents($config_file_path);

            // Loop through each domain and remove the corresponding Host blocks if they exist
            foreach ($domains as $domain) {
                // Remove Host blocks with the Hostname $domain
                if (preg_match("/^Host[^\n]*\n(.*\n)*?Hostname $domain.*?\n(?=Host|$)/sm", $config_content)) {
                    $pattern = "/^Host[^\n]*\n(.*\n)*?Hostname $domain.*?\n(?=Host|$)/sm";
                    $config_content = preg_replace($pattern, '', $config_content);
                }

                // Remove Host blocks with the Host $domain
                if (preg_match("/^Host $domain.*?\n(?=Host|$)/sm", $config_content)) {
                    $pattern = "/^Host $domain.*?\n(?=Host|$)/sm";
                    $config_content = preg_replace($pattern, '', $config_content);
                }
            }

            // Append the new config entry to the config file content
            $config_content .= $config_entry;
        } else {
            // If the config file doesn't exist, create it with the new config entry
            $config_content = $config_entry;
        }


        // Write the config file content back to the config file
        file_put_contents($config_file_path, $config_content);

        // Save the public key
        file_put_contents($public_key_path, $public_key);

        return $public_key;
    }

}