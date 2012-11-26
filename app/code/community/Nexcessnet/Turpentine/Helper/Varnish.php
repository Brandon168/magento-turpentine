<?php

/**
 * Nexcess.net Turpentine Extension for Magento
 * Copyright (C) 2012  Nexcess.net L.L.C.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

class Nexcessnet_Turpentine_Helper_Varnish extends Mage_Core_Helper_Abstract {

    /**
     * Get whether Varnish caching is enabled or not
     *
     * @return bool
     */
    public function getVarnishEnabled() {
        return (bool)Mage::getStoreConfig(
            'turpentine_varnish/general/enable_varnish' );
    }

    /**
     * Get whether Varnish debugging is enabled or not
     *
     * @return bool
     */
    public function getVarnishDebugEnabled() {
        return (bool)Mage::getStoreConfig(
            'turpentine_varnish/general/varnish_debug' );
    }

    /**
     * Get a Varnish management socket
     *
     * @param  string $host           [description]
     * @param  string|int $port           [description]
     * @param  string $secretKey=null [description]
     * @param  string $version=null   [description]
     * @return Nexcessnet_Turpentine_Model_Varnish_Admin_Socket
     */
    public function getSocket( $host, $port, $secretKey=null, $version=null ) {
        $socket = Mage::getModel( 'turpentine/varnish_admin_socket',
            array( 'host' => $host, 'port' => $port ) );
        if( $secretKey ) {
            $socket->setAuthSecret( $secretKey );
        }
        if( $version ) {
            $socket->setVersion( $version );
        }
        return $socket;
    }

    /**
     * Get management sockets for all the configured Varnish servers
     *
     * @return array
     */
    public function getSockets() {
        $sockets = array();
        $servers = Mage::helper( 'turpentine/data' )->cleanExplode( PHP_EOL,
            Mage::getStoreConfig( 'turpentine_varnish/servers/server_list' ) );
        $key = str_replace( '\n', PHP_EOL,
            Mage::getStoreConfig( 'turpentine_varnish/servers/auth_key' ) );
        $version = Mage::getStoreConfig( 'turpentine_varnish/servers/version' );
        if( $version == 'auto' ) {
            $version = null;
        }
        foreach( $servers as $server ) {
            $parts = explode( ':', $server );
            $sockets[] = $this->getSocket( $parts[0], $parts[1], $key, $version );
        }
        return $sockets;
    }
}