<?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; ?>
<div class="opalestate-search-properties">
	<div class="inner">
		<div id="opalestate-map-preview" style="height:600px;" data-page="<?php echo $paged; ?>">
			 <div id="mapView">
		        <div class="mapPlaceholder">
		        	<div class="sk-folding-cube">
						<div class="sk-cube1 sk-cube"></div>
					  	<div class="sk-cube2 sk-cube"></div>
					  	<div class="sk-cube4 sk-cube"></div>
					  	<div class="sk-cube3 sk-cube"></div>
					</div>
		        </div>
		    </div>
		</div>
		<div class="search-properies-form container">
			<?php OpalEstate_Search::render_horizontal_form(); ?> 
		</div>
	</div>
</div>	
