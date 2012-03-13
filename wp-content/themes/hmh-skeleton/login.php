<?php
	
get_header(); ?>	
 
	<div class="content">

		<form method="post">

	   	    <p>
	    	    <label>Username:</label>
	        	<input type="text" name="user_login" />
	    	</p>

	    	<p>
	       	 	<label>Password:</label>
	        	<input type="password" name="user_password" />
	    	</p>

	    		<input type="hidden" name="username" />

	    	<p>
	       		<input type="submit" value="Submit" />
	    	</p>

	    </form>

	</div>
	
<?php 
get_footer();