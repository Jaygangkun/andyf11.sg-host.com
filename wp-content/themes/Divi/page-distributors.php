<?php
/**
* Template Name: Distributors Page
*
* @package WordPress
* @subpackage 
* @since 
*/

?>
<?php

get_header();

$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() );

?>

<div id="main-content">

<?php if ( ! $is_page_builder_used ) : ?>

	<div class="container">
		<div id="content-area" class="clearfix">
			<div id="left-area">

<?php endif; ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<?php if ( ! $is_page_builder_used ) : ?>

					<h1 class="entry-title main_title"><?php the_title(); ?></h1>
				<?php
					$thumb = '';

					$width = (int) apply_filters( 'et_pb_index_blog_image_width', 1080 );

					$height = (int) apply_filters( 'et_pb_index_blog_image_height', 675 );
					$classtext = 'et_featured_image';
					$titletext = get_the_title();
					$alttext = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
					$thumbnail = get_thumbnail( $width, $height, $classtext, $alttext, $titletext, false, 'Blogimage' );
					$thumb = $thumbnail["thumb"];

					if ( 'on' === et_get_option( 'divi_page_thumbnails', 'false' ) && '' !== $thumb )
						print_thumbnail( $thumb, $thumbnail["use_timthumb"], $alttext, $width, $height );
				?>

				<?php endif; ?>

					<div class="entry-content">						
						<div class="et_pb_row et_pb_row_1 et_pb_gutters3 et_pb_row_1-4_1-2_1-4">
							<div class="search-wrap">
								<input type="text" class="" name="search" id="search">
								<span class="custom-btn" id="btn_search">Search</span>
							</div>
							<div class="map-wrap">
								<div id="map"></div>
							</div>
							<div class="distributors-wrap">
								<div class="distributors-list" id="distributors_list">
									<?php
									$distributors = get_posts(array(
										'post_type' => 'distributor',
										'numberposts' => -1
									));

									$distributor_index = 0;
									foreach($distributors as $distributor) {
										?>
										<div class="distributor-row" data-index="<?php echo $distributor_index?>">
											<h4 class="distributor-name"><?php echo get_field('name', $distributor->ID)?></h4>
											<div class="distributor-address"><?php echo get_field('address', $distributor->ID)?></div>
											<div class="distributor-address"><?php echo get_field('city', $distributor->ID)?> <?php echo get_field('state', $distributor->ID)?> <?php echo get_field('zip', $distributor->ID)?></div>
											<div class="distributor-phone">Phone <a href="tel:<?php echo get_field('phone', $distributor->ID)?>"><?php echo get_field('phone', $distributor->ID)?></a></div>
										</div>
										<?php
										$distributor_index++;
									}
									?>
									
								</div>
							</div>
						</div>
					</div>

				<?php
					if ( ! $is_page_builder_used && comments_open() && 'on' === et_get_option( 'divi_show_pagescomments', 'false' ) ) comments_template( '', true );
				?>

				</article>

			<?php endwhile; ?>

<?php if ( ! $is_page_builder_used ) : ?>

			</div>

			<?php get_sidebar(); ?>
		</div>
	</div>

<?php endif; ?>

</div>
<style>
body.custom-background {
	background-color: transparent;
}

.map-wrap {
	width: 100%;
	height: 0px;
	padding-top: 40%;
	position: relative;
}

#map {
	position: absolute;
	left: 0px;
	top: 0px;
	width: 100%;
	height: 100%;
}

.search-wrap {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 20px;
}

.search-wrap input {
	flex-grow: 1;
	margin-right: 20px;
	padding: 10px;
}

.custom-btn {
	background-color: #313476;
	color: #ffffff;
	font-size: 15px;
	padding: 10px 20px;
	display: inline-block;
	cursor: pointer;
}

.distributors-wrap {
	margin-top: 20px;
	overflow: auto;
	max-height: 400px;
}

.distributor-row {
    border: 1px solid rgba(0,0,0,.05);
	margin-bottom: 5px;
	border-radius: 3px;
	padding: 10px;
}

.distributor-name {
	font-weight: bold;
}

</style>
<script>

var map;
var geocoder;
var markers = {};
var lastInfoWindow;

function initMap() {
	const myLatLng = { lat: -25.363, lng: 131.044 };

	map = new google.maps.Map(document.getElementById("map"), {
		center: { lat: 40.116386, lng: -101.299591 },
		zoom: 5,
		gestureHandling: 'greedy',
		// mapTypeId: google.maps.MapTypeId.HYBRID
	});

	geocoder = new google.maps.Geocoder();

	<?php
	$distributor_index = 0;
	foreach($distributors as $distributor) {
		?>
		geocoder.geocode( { 'address': '<?php echo get_field('address', $distributor->ID)?> <?php echo get_field('city', $distributor->ID)?> <?php echo get_field('state', $distributor->ID)?> <?php echo get_field('zip', $distributor->ID)?>'}, function(results, status) {
			if (status == 'OK') {
				map.setCenter(results[0].geometry.location);
				var marker = new google.maps.Marker({
					map: map,
					position: results[0].geometry.location
				});

				marker.addListener("click", (e) => {
					var contentString = '<div class="map-info-content">' +
						'<h6 class="map-info-name"><?php echo get_field('name', $distributor->ID) ?></h6>' + 
						'<div class="map-info-address"><?php echo get_field('address', $distributor->ID)?></div>' + 
						'<div class="map-info-address"><?php echo get_field('city', $distributor->ID)?> <?php echo get_field('state', $distributor->ID)?> <?php echo get_field('zip', $distributor->ID)?></div>' + 
						'<div class="map-info-phone">Phone <a haref="tel:<?php echo get_field('phone', $distributor->ID)?>"><?php echo get_field('phone', $distributor->ID)?></a></div>' + 
					'</div>';

					var infoWindow = new google.maps.InfoWindow({
						content: contentString,
					});
					
					if(lastInfoWindow) {
						lastInfoWindow.close();
					}

					lastInfoWindow = infoWindow;
					
					infoWindow.open({
						anchor: marker,
						map,
						shouldFocus: false,
					});

					map.setCenter(marker.getPosition());
				});

				markers[<?php echo $distributor_index?>] = marker;

			} else {
				console.log('Geocode was not successful for the following reason: ' + status);
			}
		});
		<?php
		$distributor_index++;
	}
	?>
	
	jQuery(document).on('click', '.distributor-row', function() {
		var index = jQuery(this).attr('data-index');
		google.maps.event.trigger(markers[index], 'click');
	})

	jQuery(document).on('click', '#btn_search', function() {
		if($('#search').val() == '') {
			return;
		}

		jQuery.ajax({
			url: '<?php echo admin_url('admin-ajax.php')?>',
			type: 'post',
			data: {
				action: 'search_distributor',
				keyword: $('#search').val()
			},
			dataType: 'json',
			success: function(resp) {
				console.log('resp:', resp);

				Object.keys(markers).forEach((key) => {
					markers[key].setMap(null);
				})
				
				markers = {};

				jQuery('#distributors_list').html(resp.distributors_list_html);
				var distributors = resp.distributors;
				distributors.forEach((distributor) => {
					geocoder.geocode( { 'address': distributor['address'] + ' ' + distributor['city'] + ' ' + distributor['state'] + ' ' + distributor['zip']}, function(results, status) {
						if (status == 'OK') {
							map.setCenter(results[0].geometry.location);
							var marker = new google.maps.Marker({
								map: map,
								position: results[0].geometry.location
							});
							
							marker.addListener("click", (e) => {
								var contentString = '<div class="map-info-content">' +
									'<h6 class="map-info-name">' + distributor['name'] + '</h6>' + 
									'<div class="map-info-address">' + distributor['address'] + '</div>' + 
									'<div class="map-info-address">' + distributor['city'] + ' ' + distributor['state'] + ' ' + distributor['zip'] + '</div>' + 
									'<div class="map-info-phone">Phone <a haref="tel:' + distributor['phone'] + '">' + distributor['phone'] + '</a></div>' + 
								'</div>';

								var infoWindow = new google.maps.InfoWindow({
									content: contentString,
								});
								
								if(lastInfoWindow) {
									lastInfoWindow.close();
								}

								lastInfoWindow = infoWindow;
								
								infoWindow.open({
									anchor: marker,
									map,
									shouldFocus: false,
								});

								map.setCenter(marker.getPosition());
							});

							markers[distributor['index']] = marker;

						} else {
							console.log('Geocode was not successful for the following reason: ' + status);
						}
					});
				})
			}
		})
	})
}

</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC8VAA_3yPgtp0sorc72qN2YLV58vQN0to&callback=initMap&libraries=places&v=weekly" defer></script>
<?php

get_footer();
