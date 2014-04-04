<?php
/*
	Copyright 2014 Michael Cannon (email: mc@aihr.us)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

require_once TW_DIR_LIB . 'aihrus-framework/aihrus-framework.php';


function tw_requirements_check() {
	$check_okay = get_transient( 'tw_requirements_check' );
	if ( $check_okay !== false ) {
		return $check_okay;
	}

	$deactivate_reason = false;
	if ( ! function_exists( 'aihr_check_aihrus_framework' ) ) {
		$deactivate_reason = esc_html__( 'Missing Aihrus Framework' );
		add_action( 'admin_notices', 'tw_notice_aihrus' );
	} elseif ( ! aihr_check_aihrus_framework( TW_BASE, TW_NAME, TW_AIHR_VERSION ) ) {
		$deactivate_reason = esc_html__( 'Old Aihrus Framework version detected' );
	}

	if ( ! aihr_check_php( TW_BASE, TW_NAME ) ) {
		$deactivate_reason = esc_html__( 'Old PHP version detected' );
	}

	if ( ! aihr_check_wp( TW_BASE, TW_NAME ) ) {
		$deactivate_reason = esc_html__( 'Old WordPress version detected' );
	}

	if ( ! empty( $deactivate_reason ) ) {
		aihr_deactivate_plugin( TW_BASE, TW_NAME, $deactivate_reason );
	}

	$check_okay = empty( $deactivate_reason );
	delete_transient( 'tw_requirements_check' );
	set_transient( 'tw_requirements_check', $check_okay, WEEK_IN_SECONDS );

	return $check_okay;
}


function tw_notice_aihrus() {
	$help_url  = esc_url( 'https://aihrus.zendesk.com/entries/35689458' );
	$help_link = sprintf( __( '<a href="%1$s">Update plugins</a>. <a href="%2$s">More information</a>.', 'testimonials-widget' ), self_admin_url( 'update-core.php' ), $help_url );

	$text = sprintf( esc_html__( 'Plugin "%1$s" has been deactivated as it requires a current Aihrus Framework. Once corrected, "%1$s" can be activated. %2$s', 'testimonials-widget' ), TW_NAME, $help_link );

	aihr_notice_error( $text );
}

?>
