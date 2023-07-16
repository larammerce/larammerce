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

        $new_key_created = false;

        if ($private_key == null) {
            // Create the keypair
            $config = array(
                "digest_alg" => "sha256",
                "private_key_bits" => 2048,
                "private_key_type" => OPENSSL_KEYTYPE_RSA,
            );
            $res = openssl_pkey_new($config);

            // Get private key
            openssl_pkey_export($res, $private_key);

            // Get public key
            $pub_key = openssl_pkey_get_details($res);
            $public_key = $pub_key["key"];

            $new_key_created = true;
        } else {
            // If a private key was provided, get the public key from it
            $private_key_resource = openssl_pkey_get_private($private_key);
            $details = openssl_pkey_get_details($private_key_resource);
            $public_key = $details['key'];
        }

        // Prepare the private and public key files
        $private_key_path = "$ssh_path/larammerce_auto.pem";
        $public_key_path = "$ssh_path/larammerce_auto.pub.pem";

        // Write the private key to a file
        file_put_contents($private_key_path, $private_key);

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