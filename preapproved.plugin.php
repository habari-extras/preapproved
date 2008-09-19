<?php

/*
 * PreApproved Class
 *
 * This class allows us to auto-approve comments
 *
 */

class PreApproved extends Plugin
{
	/*
	 * function info
	 * Returns information about this plugin
	 * @return array Plugin info array
	 */
	function info()
	{
		return array (
			'name' => 'PreApproved',
			'url' => 'http://habariproject.org/',
			'author' => 'Habari Community',
			'authorurl' => 'http://habariproject.org/',
			'version' => '1.3.1',
			'description' => 'Automatically approve comments based on the number of approved comments the commenter has previously made.',
			'license' => 'Apache License 2.0',
		);
	}

	/*
	 * Register the PreApproved event type with the event log
	 */
	public function action_plugin_activation( $file )
	{
		if ( realpath( $file ) == __FILE__ ) {
			EventLog::register_type( 'PreApproved' );
			if ( !( Options::get( 'preapproved__approved_count' ) ) ) {
				Options::set( 'preapproved__approved_count', 1 );
			}
		}
	}

	/*
	 * Unregister the PreApproved event type on deactivation if it isn't being used
	 */
	public function action_plugin_deactivation( $file )
	{
		if ( realpath( $file ) == __FILE__ ) {
			EventLog::unregister_type( 'PreApproved' );
		}
	}

	public function filter_plugin_config($actions, $plugin_id)
	{
		if ( $plugin_id == $this->plugin_id() ) {
			$actions[]= _t( 'Configure' );
		}
		return $actions;
	}

	public function action_plugin_ui($plugin_id, $action)
	{
		if ( $plugin_id == $this->plugin_id() ) {
			switch ( $action ) {
				case _t( 'Configure' ):
					$form = new FormUI( 'preapproved' );
					$form->append( 'text', 'approved_count', 'option:preapproved__approved_count', _t( 'Required number of approved comments: ' ) );
					$form->approved_count->add_validator( array( $this, 'validate_integer' ) );
					$form->append( 'submit', 'save', _t( 'Save' ) );
					$form->set_option( 'success_message', _t( 'Configuration saved' ) );
					$form->out();
				break;
			}
		}
	}

	/*
	 * function act_comment_insert_before
	 * This function is executed when the action "comment_insert_before"
	 * is invoked from a Comment object.
	 * The parent class, Plugin, handles registering the action
	 * and hook name using the name of the function to determine
	 * where it will be applied.
	 * You can still register functions as hooks without using
	 * this method, but boy, is it handy.
	 * @param Comment The comment that will be processed before storing it in the database.
	 * @return Comment The comment result to store.
	 */
	function action_comment_insert_before ( $comment )
	{
		// This plugin ignores non-comments and comments already marked as spam
		if( $comment->type == Comment::COMMENT && $comment->status != Comment::STATUS_SPAM) {
			if( Comments::get( array( 'email' => $comment->email, 'name' => $comment->name,
								'url' => $comment->url, 'status' => Comment::STATUS_APPROVED ) )->count >= Options::get( 'preapproved__approved_count' ) ) {
				$comment->status = Comment::STATUS_APPROVED;
				EventLog::log( 'Comment by ' . $comment->name . ' automatically approved.', 'info', 'PreApproved', 'PreApproved' );
			}
		}
		return $comment;
	}

	function set_priorities()
	{
	  return array( 'action_comment_insert_before' => 10 );
	}

	/*
	* Add update beacon support
	*/
	function action_update_check()
	{
		Update::add( 'PreApproved', '0fa22c74-a0d6-11dc-8314-0800200c9a66', $this->info->version );
	}

	/*
	 * A validation function that returns an error if the value passed in is not  an integer
	 *
	 * @param string $value A value to test if it is an integer
	 * @param formcontrol $control The control containing the value
	 * @param formui $form The form containing the control
	 * @return array An empty array if the value is an integer or an array with strings describing the errors
	 */
	function validate_integer( $value, $control, $form )
	{
		if( !ctype_digit( $value ) ) {
			return array( _t( 'Please enter a valid positive integer.' ) );
		}
		$val = intval( $value );
		if( !is_int( $val ) ) {
			return array( _t( 'Please enter a valid positive integer.' ) );
		}
		return array();
	}

}
?>
