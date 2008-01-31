<?php

/**
 * PreApproved Class
 * 
 * This class allows us to auto-approve previously approved commenters
 *
 **/

class PreApproved extends Plugin
{
	/**
	 * function info
	 * Returns information about this plugin
	 * @return array Plugin info array
	 **/
	function info()
	{
		return array (
			'name' => 'PreApproved',
			'url' => 'http://habariproject.org/',
			'author' => 'Habari Community',
			'authorurl' => 'http://habariproject.org/',
			'version' => '1.1',
			'description' => 'Automatically approve commenters that have approved comments in the database.',
			'license' => 'Apache License 2.0',
		);
	}
	
	/**
	 * Register the PreApproved event type with the event log
	 */	 
	public function action_plugin_activation( $file ) {
		if ( realpath( $file ) == __FILE__ ) {
			EventLog::register_type( 'PreApproved' );
		}
	}
	
	/**
	 * Unregister the PreApproved event type on deactivation
	 * @todo Should we be doing this?
	 */	 	 
	public function action_plugin_deactivation( $file ) {
		if ( realpath( $file ) == __FILE__ ) {
			EventLog::unregister_type( 'PreApproved' );
		}
	}
	
	/**
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
	 **/	 	 	 	 	
	function action_comment_insert_before ( $comment ) {
		// This plugin ignores non-comments
		if( $comment->type != Comment::COMMENT ) {
			return $comment;
		}
		
		// <script> is bad, mmmkay?
		$comment->content = InputFilter::filter( $comment->content );

		if( Comments::get( array( 'email' => $comment->email, 'name' => $comment->name, 
							'url' => $comment->url, 'status' => Comment::STATUS_APPROVED ) )->count ) {
			$comment->status = Comment::STATUS_APPROVED;
			EventLog::log( 'Comment by ' . $comment->name . ' automatically approved.', 'info', 'comment', 'preapproved' );
		}
		return;
	}
	
	function set_priorities() {
	  return array( 'action_comment_insert_before' => 10 );
	}
	
	/**
     * Add update beacon support 
     **/
    
    function action_update_check() {
      Update::add( 'PreApproved', '0fa22c74-a0d6-11dc-8314-0800200c9a66', $this->info->version ); 
    }
}
?>
