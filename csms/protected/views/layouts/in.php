<?php $this->beginContent('/layouts/main');?>
<div id="body-wrapper"> <!-- Wrapper for the radial gradient background -->	
		<div id="main-content"> <!-- Main Content Section with everything -->
			
			<noscript> <!-- Show a notification if the user has disabled javascript -->
				<div class="notification error png_bg">
					<div>
						Javascript is disabled or is not supported by your browser. Please <a href="http://browsehappy.com/" title="Upgrade to a better browser">upgrade</a> your browser or <a href="http://www.google.com/support/bin/answer.py?answer=23852" title="Enable Javascript in your browser">enable</a> Javascript to navigate the interface properly.
					</div>
				</div>
			</noscript>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
				    'links'=>$this->breadcrumbs,
					'separator'	=>	' > ',
					'homeLink'	=>	CHtml::link('运营系统',array('/')),
	        		'htmlOptions'	=>	array('class'=>'breadcrumb')
				));
			?>
		<?php echo $content;?>			
		</div> <!-- End #main-content -->
	</div>
<?php $this->endContent();?>