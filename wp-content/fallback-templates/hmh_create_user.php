<?php get_header(); ?>

<div class="content">
	
	
	<h2>Add a new user to the HM Holidays system</h2>
	
	<form id="create_user_form" method="post">
		
		<span class="prompt">Select a user</span>
		<p>
			<select name="hmh_user_id">
				<option value="0"> - Select - </option>
				<?php foreach( get_users() as $user ): ?>
			
					<option value="<?php echo $user->ID; ?>"><?php echo $user->display_name;?></option>
			
				<?php endforeach; ?>
		
			</select>
		</p>
		
		<span class="prompt">or create one</span>
		<p>
			<input name="hmh_username" type="text" value="" placeholder="Username" />
		</p>
		<p>
			<input name="hmh_password" type="text" value="" placeholder="Password" />
		</p>	
		<p>
			<input name="hmh_email" type="text" value="" placeholder="Email" > 
		</p>

		<span class="prompt">when did they start working?</span>
		<p>
			<input type="date" name="hmh_start_date" value="" placeholder="Date" />
		</p>
		
		<span class="prompt">how many days have they had off already?</span>
		<p>
			<input type="text" value="" name="hmh_offset" placeholder="Number of days" />
		</p>
	
		<span class="prompt">How many holidays do they get per year?</span>
		<p>
			<input type="text" value="" name="hmh_per_year" placeholder="Holidays (in days)" />
		</p>
		
		<p>
			<input type="submit" value="Create" />
		</p>

				
	</form>
	
</div>

<?php get_footer(); ?>