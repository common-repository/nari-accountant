<?php

$nari_ver_option = get_option( 'nari100_version');
if ($nari_ver_option == "" || $nari_ver_option <= NARI_ACCOUNTANT_VERSION){

    //Do some code for update

    update_option('nari100_version', NARI_ACCOUNTANT_VERSION);
    nari100_activate('forced activation');
}