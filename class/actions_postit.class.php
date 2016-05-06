<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    class/actions_postit.class.php
 * \ingroup postit
 * \brief   This file is an example hook overload class file
 *          Put some comments here
 */

/**
 * Class Actionspostit
 */
class Actionspostit
{
	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var array Errors
	 */
	public $errors = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
	}

	function note($fk_object, $type_object) {
		
		global $langs;
		
			$langs->load('postit@postit');
		
			$a = '<a id="addNote" href="javascript:addNote()" style="position:absolute; left:-30px; top:0px; display:block;">'.img_picto('', 'post-it.png@postit',' style="width:32px; height:32px;" ').'</a>';
			
			?>
			<script language="javascript">
				$(document).ready(function() {
					$a = $('<?php echo $a ?>');
					$('div.login_block_other').append($a);	
					
					$.ajax({
						url:"<?php echo dol_buildpath('/postit/script/interface.php',1) ?>"
						,data: {
							get:'postit-of-object'
							,fk_object:<?php echo $fk_object ?>
							,type_object:"<?php echo $type_object ?>"
						
						}
						,dataType:"json"
					}).done(function(Tab) {
						console.log(Tab);
						for(x in Tab) {
							addNote(Tab[x]);
						}
						
					});
									
				});
				
				function addNote(postit) {
					$a = $('#addNote');
					pos = $a.offset();
					console.log(pos);
					$div = $('<div class="yellowPaperTemporary postit"><div rel="content"><div rel="delete"><?php echo addslashes(img_delete()) ?></div><div rel="postit-title"><?php echo $langs->trans('NewNote') ?></div><div rel="postit-comment"><?php echo $langs->trans('NoteComment') ?></div></div></div>');
					
					$div.css('width',  100);
					$div.css('height', 200);
					$div.css('top', pos.top + 20);
					$div.css('left', pos.left - 50);   
					
					if(postit) {
						$div.attr('id-post-it', postit.rowid);
						if(postit.position_top<=0)postit.position_top = 0;
						if(postit.position_left<=0)postit.position_left = 0;
						if(postit.position_width<=0)postit.position_width= 100;
						if(postit.position_height<=0)postit.position_height = 200;

						$div.find('[rel=postit-title]').html(postit.title);
						$div.find('[rel=postit-comment]').html(postit.comment);
						
						$div.css('top',  postit.position_top);
						$div.css('left',  postit.position_left);
						$div.css('width',  postit.position_width);
						$div.css('height',  postit.position_height);
					}
					
					var option = {type : "textarea", action : "click"};
					//todo factorise
					$div.find('[rel=postit-title]').editable("click", function(e){
					  var $div = e.target.closest('div.postit');
					  var idPostit = $div.attr('id-post-it');
				  	  $.ajax({
							url:"<?php echo dol_buildpath('/postit/script/interface.php',1) ?>"
							,data: {
								put:'postit'
								,id:idPostit
								,fk_object:<?php echo $fk_object ?>
								,type_object:"<?php echo $type_object ?>"
								,title:e.value
							}
							,method:'post'
						}).done(function(idPostit) {
							$div.attr('id-post-it', idPostit);
						});
					});
					
					$div.find('[rel=postit-comment]').editable(option, function(e){
					  var $div = e.target.closest('div.postit');
					  var idPostit = $div.attr('id-post-it');
					
				  	  $.ajax({
							url:"<?php echo dol_buildpath('/postit/script/interface.php',1) ?>"
							,data: {
								put:'postit'
								,id:idPostit
								,fk_object:<?php echo $fk_object ?>
								,type_object:"<?php echo $type_object ?>"
								,comment:e.value
							}
							,method:'post'
						}).done(function(idPostit) {
							$div.attr('id-post-it', idPostit);
						});

					});
					
					$div.find('[rel=delete]').click(function() {
					
							var $div = $(this).closest('div.postit');
							var idPostit = $div.attr('id-post-it');
							console.log($div,idPostit);
							$.ajax({
								url:"<?php echo dol_buildpath('/postit/script/interface.php',1) ?>"
								,data: {
									put:'delete'
									,id:idPostit
									
								}
								,method:'post'
							}).done(function(idPostit) {
								$div.remove();
							});
							
						
					});
					
					$('body').append($div);
					
					$div.draggable({
						stop:function(event, ui) {
							
							var $div = $(this);
							var idPostit = $(this).attr('id-post-it');
							
							$.ajax({
								url:"<?php echo dol_buildpath('/postit/script/interface.php',1) ?>"
								,data: {
									put:'postit'
									,id:idPostit
									,fk_object:<?php echo $fk_object ?>
									,type_object:"<?php echo $type_object ?>"
									,top:ui.position.top
									,left:ui.position.left
								}
								,method:'post'
							}).done(function(idPostit) {
								$div.attr('id-post-it', idPostit);
							});
							
						}
					});
					
					
					$div.resizable({
						stop:function(event, ui) {
							
							var $div = $(this);
							var idPostit = $(this).attr('id-post-it');
							
							$.ajax({
								url:"<?php echo dol_buildpath('/postit/script/interface.php',1) ?>"
								,data: {
									put:'postit'
									,id:idPostit
									,fk_object:<?php echo $fk_object ?>
									,type_object:"<?php echo $type_object ?>"
									,top:ui.position.top
									,left:	ui.position.left
									,width:ui.size.width
									,height:ui.size.height
								}
								,method:'post'
							}).done(function(idPostit) {
								$div.attr('id-post-it', idPostit);
							});
							
						}
					});
					
					
				}
				
				
			</script>
			<?php
		
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	function addStatisticLine($parameters, &$object, &$action, $hookmanager) {
		
		if (in_array('index', explode(':', $parameters['context'])))
		{
			
			$this->note(-1, 'global');
	
		}
			
	}
	
	function formObjectOptions($parameters, &$object, &$action, $hookmanager)
	{
		$error = 0; // Error counter
		
		if (in_array('globalcard', explode(':', $parameters['context'])) && $object->id>0)
		{
					
			$this->note($object->id, $object->element);
			
		}

		if (! $error)
		{
			return 0; // or return 1 to replace standard code
		}
		else
		{
			return -1;
		}
	}
}