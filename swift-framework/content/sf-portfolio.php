<?php

    /*
    *
    *	Swift Page Builder - Portfolio Items Function Class
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    *	sf_portfolio_items()
    *	sf_portfolio_filter()
    *	sf_portfolio_thumbnail()
    *	sf_portfolio_item_link()
    *
    */

    /* PORTFOLIO ITEMS
    ================================================== */
    if ( ! function_exists( 'sf_portfolio_items' ) ) {
        function sf_portfolio_items( $atts ) {

			extract( shortcode_atts( array(
			    'title'              => '',
			    'display_type'       => '',
			    'multi_size_ratio'   => '',
			    'fullwidth'          => '',
			    'gutters'            => '',
			    'columns'            => '',
			    'show_title'         => '',
			    'show_subtitle'      => '',
			    'show_excerpt'       => '',
			    'hover_show_excerpt' => '',
			    'excerpt_length'     => '',
			    'item_count'         => '',
			    'category'           => '',
			    'order'              => '',
			    'order_by'           => '',
			    'portfolio_filter'   => '',
			    'pagination'         => '',
			    'button_enabled'     => '',
			    'hover_style'        => '',
			    'post_type'			 => 'portfolio',
			    'el_position'        => '',
			    'width'              => '',
			    'el_class'           => ''
			), $atts ) );

            /* OUTPUT VARIABLE
            ================================================== */
            $portfolio_items_output = $grid_size = "";
            $count                  = 0;


            /* CATEGORY SLUG MODIFICATION
            ================================================== */
            if ( $category == "All" ) {
                $category = "all";
            }
            if ( $category == "all" ) {
                $category = '';
            }
            $category_slug = str_replace( '_', '-', $category );


            /* PORTFOLIO QUERY SETUP
            ================================================== */
            global $post, $wp_query;

            if ( get_query_var( 'paged' ) ) {
                $paged = get_query_var( 'paged' );
            } elseif ( get_query_var( 'page' ) ) {
                $paged = get_query_var( 'page' );
            } else {
                $paged = 1;
            }

            $portfolio_args = array(
                'post_type'          => $post_type,
                'post_status'        => 'publish',
                'paged'              => $paged,
                'portfolio-category' => $category_slug,
                'posts_per_page'     => $item_count,
                'order'         	 => $order,
                'orderby'        	 => $order_by,
            );

            $portfolio_items = new WP_Query( $portfolio_args );


            /* LIST CLASS CONFIG
            ================================================== */
            $list_class = '';
            if ( $display_type == "masonry" || $display_type == "masonry-gallery" ) {
                $list_class .= 'masonry-items filterable-items col-' . $columns . ' row clearfix';
            } else if ( $display_type == "gallery" ) {
                $list_class .= 'gallery-portfolio filterable-items col-' . $columns . ' row clearfix';
            } else if ( $display_type == "multi-size-masonry" ) {
                $columns = 3;
                $list_class .= 'multi-masonry-items filterable-items col-' . $columns . ' row clearfix';
            } else {
                $list_class .= 'standard-portfolio filterable-items col-' . $columns . ' row clearfix';
            }

            // Full width
            if ( $fullwidth == "yes" ) {
                $list_class .= ' portfolio-full-width';
            }

            // Gutters
            if ( $gutters == "no" ) {
                $list_class .= ' no-gutters';
            } else {
                $list_class .= ' gutters';
            }

            // Thumb Type
            if ( $hover_style == "default" && function_exists( 'sf_get_thumb_type' ) ) {
                $list_class .= ' ' . sf_get_thumb_type();
            } else {
                $list_class .= ' thumbnail-' . $hover_style;
            }


            if ( $display_type == "multi-size-masonry" ) {
                if ( $fullwidth == "yes" ) {
                    $grid_size = 'col-sm-3';
                } else {
                    $grid_size = 'col-sm-4';
                }
            }


            /* ITEMS OUTPUT
            ================================================== */
            global $sf_options;

            $portfolio_items_output .= '<ul class="portfolio-items ' . $list_class . '">' . "\n";

            if ( $display_type == "multi-size-masonry" ) {
                $portfolio_items_output .= '<li class="clearfix portfolio-item ' . $grid_size . ' grid-sizer">' . "\n";
            }

            while ( $portfolio_items->have_posts() ) : $portfolio_items->the_post();


                /* META VARIABLES
                ================================================== */
                $thumb_type     = sf_get_post_meta( $post->ID, 'sf_thumbnail_type', true );
                $item_title     = get_the_title();
                $item_subtitle  = sf_get_post_meta( $post->ID, 'sf_portfolio_subtitle', true );
                $permalink      = get_permalink();
                $custom_excerpt = sf_get_post_meta( $post->ID, 'sf_custom_excerpt', true );
                $post_excerpt   = '';
                if ( $custom_excerpt != '' ) {
                    $post_excerpt = sf_custom_excerpt( $custom_excerpt, $excerpt_length );
                } else {
                    $post_excerpt = sf_excerpt( $excerpt_length );
                }

				$taxonomy_name = 'category';
				if ( $post_type != "post") {
					$taxonomy_name = $post_type . '-category';
				}
				if ( $taxonomy_name == "product-category" ) {
					$taxonomy_name = "product_cat";
				}
                $post_terms = get_the_terms( $post->ID, $taxonomy_name );
                $term_slug  = " ";

                if ( ! empty( $post_terms ) ) {
                    foreach ( $post_terms as $post_term ) {
                        $term_slug = $term_slug . strtolower($post_term->slug) . ' ';
                    }
                }


                /* COLUMN VARIABLE CONFIG
                ================================================== */
                $item_class = "";

                if ( $columns == "1" ) {
                    $item_class = "col-sm-12 ";
                } else if ( $columns == "2" ) {
                    $item_class = "col-sm-6 ";
                } else if ( $columns == "3" ) {
                    $item_class = "col-sm-4 ";
                } else if ( $columns == "4" ) {
                    $item_class = "col-sm-3 ";
                } else if ( $columns == "5" ) {
                    $item_class = "col-sm-sf-5 ";
                }

                $masonry_thumb_size = sf_get_post_meta( get_the_ID(), 'sf_masonry_thumb_size', true );

                if ( $display_type == "multi-size-masonry" ) {
                    if ( $fullwidth == "yes" ) {
                        if ( $masonry_thumb_size == "" ) {
                            $masonry_thumb_size = "standard";
                        }
                        if ( $masonry_thumb_size == "wide" ) {
                            $item_class = 'col-sm-6 size-wide ';
                        } else if ( $masonry_thumb_size == "tall" ) {
                            $item_class = 'col-sm-3 size-tall ';
                        } else if ( $masonry_thumb_size == "wide-tall" ) {
                            $item_class = 'col-sm-6 size-wide-tall ';
                        } else {
                            $item_class = 'col-sm-3 size-standard ';
                        }
                    } else {
                        if ( $masonry_thumb_size == "" ) {
                            $masonry_thumb_size = "standard";
                        }
                        if ( $masonry_thumb_size == "wide" ) {
                            $item_class = 'col-sm-8 size-wide ';
                        } else if ( $masonry_thumb_size == "tall" ) {
                            $item_class = 'col-sm-4 size-tall ';
                        } else if ( $masonry_thumb_size == "wide-tall" ) {
                            $item_class = 'col-sm-8 size-wide-tall ';
                        } else {
                            $item_class = 'col-sm-4 size-standard ';
                        }
                    }
                }

                /* DISPLAY TYPE CONFIG
                ================================================== */
                if ( $display_type == "masonry" || $display_type == "masonry-gallery" ) {
                    $item_class .= "masonry-item masonry-gallery-item";
                } else if ( $display_type == "gallery" ) {
                    $item_class .= "gallery-item ";
                } else if ( $display_type == "multi-size-masonry" ) {
                    $item_class .= "multi-masonry-item ";
                } else {
                    $item_class .= "standard ";
                }


                /* LINK TYPE CONFIG
                ================================================== */
                $item_link = sf_portfolio_item_link();


                /* ITEM OUTPUT
                ================================================== */
                $portfolio_items_output .= '<li itemscope itemtype="http://schema.org/CreativeWork" data-id="id-' . $count . '" class="clearfix portfolio-item ' . $item_class . ' ' . $term_slug . '">' . "\n";

				$portfolio_items_output .= apply_filters( 'sf_before_portfolio_item_thumb' , '');

                /* THUMBNAIL CONFIG
                ================================================== */
                if ( $thumb_type != "none" ) {
                    $portfolio_items_output .= sf_portfolio_thumbnail( $display_type, $masonry_thumb_size, $multi_size_ratio, $columns, $hover_show_excerpt, $excerpt_length, $gutters, $fullwidth );
                }

                $portfolio_items_output .= apply_filters( 'sf_after_portfolio_item_thumb' , '');

                if ( $display_type != "gallery" && $display_type != "masonry-gallery" && $display_type != "multi-size-masonry" ) {

                    $portfolio_items_output .= '<div class="portfolio-item-details">' . "\n";

                    if ( $show_title == "yes" ) {

                        $portfolio_items_output .= '<div class="comments-likes">';
                        if ( function_exists( 'lip_love_it_link' ) ) {
                            $portfolio_items_output .= lip_love_it_link( get_the_ID(), false );
                        }
                        $portfolio_items_output .= '</div>';

                        $portfolio_items_output .= '<h3 class="portfolio-item-title" itemprop="name headline"><a ' . $item_link['config'] . '>' . $item_title . '</a></h3>' . "\n";
                    }
                    if ( $show_subtitle == "yes" && $item_subtitle ) {
                        $portfolio_items_output .= '<h5 class="portfolio-subtitle" itemprop="name alternativeHeadline">' . $item_subtitle . '</h5>' . "\n";
                    }
                    if ( $show_excerpt == "yes" ) {
                        $portfolio_items_output .= '<div class="portfolio-item-excerpt" itemprop="description">' . $post_excerpt . '</div>' . "\n";
                    }

                    $portfolio_items_output .= '</div>' . "\n";

                }

                $portfolio_items_output .= '</li>' . "\n";

                $count ++;

            endwhile;

            wp_reset_query();
            wp_reset_postdata();

            $portfolio_items_output .= '</ul>' . "\n";


            /* PAGINATION OUTPUT
            ================================================== */
            if ( $pagination == "yes" ) {
                if ( $display_type == "masonry" || $display_type == "masonry-gallery" ) {
                    $portfolio_items_output .= '<div class="pagination-wrap masonry-pagination">';
                } else {
                    $portfolio_items_output .= '<div class="pagination-wrap">';
                }
                $portfolio_items_output .= pagenavi( $portfolio_items );
                $portfolio_items_output .= '</div>';
            }


            /* FUNCTION OUTPUT
            ================================================== */

            return $portfolio_items_output;
        }
    }


    /* PORTFOLIO FILTER
    ================================================== */
    if ( ! function_exists( 'sf_portfolio_filter' ) ) {
        function sf_portfolio_filter( $style = "basic", $post_type = "portfolio", $parent_category = "", $frontend_display = false ) {

            $filter_output = $tax_terms = "";

            $taxonomy_name = 'category';

            if ( $post_type != "post") {
            	$taxonomy_name = $post_type . '-category';
            }

            if ( $parent_category == "" || $parent_category == "All" ) {
                $tax_terms = sf_get_category_list( $taxonomy_name, 1, '', true );
            } else {
                $tax_terms = sf_get_category_list( $taxonomy_name, 1, $parent_category, true );
            }

            $filter_output .= '<div class="filter-wrap clearfix">' . "\n";
            $filter_output .= '<ul class="post-filter-tabs filtering clearfix">' . "\n";
            $filter_output .= '<li class="all selected"><a data-filter="*" href="#"><span class="item-name">' . __( "Show all", "swiftframework" ) . '</span></a></li>' . "\n";
            foreach ( $tax_terms as $tax_term ) {
                $term = get_term_by( 'slug', $tax_term, $taxonomy_name );
                if ( $term ) {
                    $filter_output .= '<li><a href="#" title="' . $term->name . '" class="' . $term->slug . '" data-filter=".' . $term->slug . '"><span class="item-name">' . $term->name . '</span></a></li>' . "\n";
                } else {
                    $filter_output .= '<li><a href="#" title="' . $tax_term . '" class="' . $tax_term . '" data-filter=".' . $tax_term . '"><span class="item-name">' . $tax_term . '</span></a></li>' . "\n";
                }
            }
            $filter_output .= '</ul></div>' . "\n";

            return $filter_output;
        }
    }

    /* PORTFOLIO THUMBNAIL
    ================================================== */
    if ( ! function_exists( 'sf_portfolio_thumbnail' ) ) {
        function sf_portfolio_thumbnail( $display_type = "gallery", $multi_size = "", $multi_size_ratio = "1/1", $columns = "2", $hover_show_excerpt = "no", $excerpt_length = 20, $gutters = "yes", $fullwidth = "no" ) {

            global $post, $sf_options;

            $portfolio_thumb = $thumb_image_id = $thumb_image = $thumb_gallery = $video = $item_class = $link_config = $port_hover_style = $port_hover_text_style = '';
            $thumb_width     = 400;
            $thumb_height    = 300;
            $video_height    = 300;

            if ( $columns == "1" ) {
                $thumb_width  = 1200;
                $thumb_height = 900;
                $video_height = 900;
            } else if ( $columns == "2" ) {
                $thumb_width  = 800;
                $thumb_height = 600;
                $video_height = 600;
            } else if ( $columns == "3" || $columns == "4" ) {
                if ( $fullwidth == "yes" ) {
                    $thumb_width  = 500;
                    $thumb_height = 375;
                    $video_height = 375;
                } else {
                    $thumb_width  = 400;
                    $thumb_height = 300;
                    $video_height = 300;
                }
            }

            if ( $display_type == "multi-size-masonry" ) {
                if ( $multi_size_ratio == "4/3" ) {
                    if ( $multi_size == "wide-tall" ) {
                        $thumb_width  = 1000;
                        $thumb_height = 750;
                    } else if ( $multi_size == "tall" ) {
                        $thumb_width  = 500;
                        $thumb_height = 750;
                    } else if ( $multi_size == "wide" ) {
                        $thumb_width  = 1000;
                        $thumb_height = 375;
                    } else if ( $multi_size == "standard" ) {
                        $thumb_width  = 500;
                        $thumb_height = 375;
                        $video_height = 375;
                    }
                } else {
                    if ( $multi_size == "wide-tall" ) {
                        $thumb_width  = 900;
                        $thumb_height = 900;
                    } else if ( $multi_size == "tall" ) {
                        $thumb_width  = 450;
                        $thumb_height = 900;
                    } else if ( $multi_size == "wide" ) {
                        $thumb_width  = 900;
                        $thumb_height = 450;
                    } else if ( $multi_size == "standard" ) {
                        $thumb_width  = 450;
                        $thumb_height = 450;
                        $video_height = 450;
                    }
                }

                if ( $gutters == "yes" && $multi_size == "tall" ) {
                    $thumb_height = $thumb_height + 50;
                }
                if ( $gutters == "yes" && $multi_size == "wide-tall" ) {
                    $thumb_height = $thumb_height + 15;
                }
            }

            if ( $display_type == "masonry" || $display_type == "masonry-gallery" ) {
                $thumb_height = null;
            }

            $thumb_type  = sf_get_post_meta( $post->ID, 'sf_thumbnail_type', true );
            $thumb_image = rwmb_meta( 'sf_thumbnail_image', 'type=image&size=full' );
            $thumb_video = sf_get_post_meta( $post->ID, 'sf_thumbnail_video_url', true );
            if ( $display_type == "multi-size-masonry" && $multi_size != "" ) {
                $thumb_gallery = rwmb_meta( 'sf_thumbnail_gallery', 'type=image&size=large-square' );
            } else {
                if ( $columns == "2" ) {
                    $thumb_gallery = rwmb_meta( 'sf_thumbnail_gallery', 'type=image&size=thumb-image-twocol' );
                } else {
                    $thumb_gallery = rwmb_meta( 'sf_thumbnail_gallery', 'type=image&size=thumb-image' );
                }
            }
            $thumb_link_type          = sf_get_post_meta( $post->ID, 'sf_thumbnail_link_type', true );
            $thumb_link_url           = sf_get_post_meta( $post->ID, 'sf_thumbnail_link_url', true );
            $thumb_lightbox_thumb     = rwmb_meta( 'sf_thumbnail_image', 'type=image&size=large' );
            $thumb_lightbox_image     = rwmb_meta( 'sf_thumbnail_link_image', 'type=image&size=large' );
            $thumb_lightbox_video_url = sf_get_post_meta( $post->ID, 'sf_thumbnail_link_video_url', true );
            $thumb_lightbox_video_url = sf_get_embed_src( $thumb_lightbox_video_url );
            $port_hover_bg_color      = sf_get_post_meta( $post->ID, 'sf_port_hover_bg_color', true );
            $port_hover_text_color    = sf_get_post_meta( $post->ID, 'sf_port_hover_text_color', true );

            if ( $port_hover_bg_color != "" ) {
                $overlay_opacity = $sf_options['overlay_opacity'];
                if ( $overlay_opacity == 100 ) {
                    $overlay_opacity = '1';
                } else {
                    $overlay_opacity = '0.' . $overlay_opacity;
                }
                $port_hover_bg_rgb = sf_hex2rgb( $port_hover_bg_color );
                $port_hover_style  = 'style="background-color:rgba(' . $port_hover_bg_rgb['red'] . ',' . $port_hover_bg_rgb['green'] . ',' . $port_hover_bg_rgb['blue'] . ',' . $overlay_opacity . ');"';
            }

            if ( $port_hover_text_color != "" ) {
                $port_hover_text_style = 'style="color: ' . $port_hover_text_color . ';"';
            }

            foreach ( $thumb_image as $detail_image ) {
                $thumb_image_id = $detail_image['ID'];
                $thumb_img_url  = $detail_image['url'];
                break;
            }

            if ( ! $thumb_image ) {
                $thumb_image    = get_post_thumbnail_id();
                $thumb_image_id = $thumb_image;
                $thumb_img_url  = wp_get_attachment_url( $thumb_image, 'full' );
            }

            $thumb_lightbox_img_url = wp_get_attachment_url( $thumb_lightbox_image, 'full' );
            $image_alt              = esc_attr( sf_get_post_meta( $thumb_image_id, '_wp_attachment_image_alt', true ) );

            $item_title     = get_the_title();
            $item_subtitle  = sf_get_post_meta( $post->ID, 'sf_portfolio_subtitle', true );
            $permalink      = get_permalink();
            $item_link      = sf_portfolio_item_link();
            $custom_excerpt = sf_get_post_meta( $post->ID, 'sf_custom_excerpt', true );
            $post_excerpt   = '';
            if ( $custom_excerpt != '' ) {
                $post_excerpt = sf_custom_excerpt( $custom_excerpt, $excerpt_length );
            } else {
                $post_excerpt = sf_excerpt( $excerpt_length );
            }


			if ( $display_type == "gallery" || $display_type == "masonry-gallery" || $display_type == "multi-size-masonry" ) {
			    $portfolio_thumb .= '<figure class="animated-overlay overlay-style">' . "\n";
			} else {
			    $portfolio_thumb .= '<figure class="animated-overlay overlay-alt">' . "\n";
			}

            if ( $thumb_type == "video" ) {

                $video = sf_video_embed( $thumb_video, $thumb_width, $video_height );
                $portfolio_thumb .= '<div class="video-thumb">' . $video . '</div>';

            } else if ( $thumb_type == "slider" ) {

                $portfolio_thumb .= '<div class="flexslider thumb-slider"><ul class="slides">' . "\n";

                foreach ( $thumb_gallery as $image ) {
                    $portfolio_thumb .= "<li><a " . $item_link['config'] . "><img src='{$image['url']}' width='{$image['width']}' height='{$image['height']}' alt='{$image['alt']}' /></a></li>" . "\n";
                }

                $portfolio_thumb .= '</ul></div>' . "\n";

            } else {

                if ( $thumb_type == "image" && $thumb_img_url == "" ) {
                    $thumb_img_url = "default";
                }

                $image = sf_aq_resize( $thumb_img_url, $thumb_width, $thumb_height, true, false );

                if ( $image ) {

                    $portfolio_thumb .= '<a ' . $item_link['config'] . '></a>';

                    if ( $display_type == "multi-size-masonry" ) {
                        $portfolio_thumb .= '<div class="multi-masonry-img-wrap"><img itemprop="image" src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '" alt="' . $image_alt . '" /></div>' . "\n";
                    } else {
                        $portfolio_thumb .= '<img itemprop="image" src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '" alt="' . $image_alt . '" />' . "\n";
                    }

                    $portfolio_thumb .= '<div class="figcaption-wrap"></div>';

                    if ( $item_subtitle != "" && $hover_show_excerpt == "no" && ( $display_type == "gallery" || $display_type == "masonry-gallery" || $display_type == "multi-size-masonry" ) ) {
                        $portfolio_thumb .= '<figcaption ' . $port_hover_style . '><div class="thumb-info">';
                    } else if ( $display_type == "standard" || $display_type == "masonry" ) {
                        $portfolio_thumb .= '<figcaption ' . $port_hover_style . '><div class="thumb-info thumb-info-alt">';
                    } else if ( $hover_show_excerpt == "yes" && ( $display_type == "gallery" || $display_type == "masonry-gallery" ) ) {
                        $portfolio_thumb .= '<figcaption ' . $port_hover_style . '><div class="thumb-info thumb-info-excerpt">';
                    } else {
                        $portfolio_thumb .= '<figcaption ' . $port_hover_style . '><div class="thumb-info">';
                    }
                    if ( $display_type == "gallery" || $display_type == "masonry-gallery" || $display_type == "multi-size-masonry" ) {
                        if ( $hover_show_excerpt == "yes" ) {
                            $portfolio_thumb .= '<h4 itemprop="name headline" ' . $port_hover_text_style . '>' . $item_title . '</h4>';
                            if ( $post_excerpt != "" ) {
                                $portfolio_thumb .= '<div class="name-divide"></div>';
                                $portfolio_thumb .= '<div itemprop="description" ' . $port_hover_text_style . '>' . $post_excerpt . '</div>';
                            }
                        } else {
                            $portfolio_thumb .= '<h4 itemprop="name headline" ' . $port_hover_text_style . '>' . $item_title . '</h4>';
                            if ( $item_subtitle != "" ) {
                                $portfolio_thumb .= '<div class="name-divide"></div>';
                                $portfolio_thumb .= '<h5 itemprop="name alternativeHeadline" ' . $port_hover_text_style . '>' . $item_subtitle . '</h5>';
                            }
                        }
                    } else {
                        $portfolio_thumb .= '<i class="' . $item_link['icon'] . '"></i>';
                    }
                    $portfolio_thumb .= '</div></figcaption>';
                }
            }

            $portfolio_thumb .= '</figure>' . "\n";

            return $portfolio_thumb;
        }
    }


    /* PORTFOLIO LINK CONFIG
    ================================================== */
    if ( ! function_exists( 'sf_portfolio_item_link' ) ) {
        function sf_portfolio_item_link() {

            $link_config = $item_icon = $thumb_img_url = "";

            global $post, $sf_options;

            $thumb_image              = rwmb_meta( 'sf_thumbnail_image', 'type=image&size=full' );
            $thumb_link_type          = sf_get_post_meta( $post->ID, 'sf_thumbnail_link_type', true );
            $thumb_link_url           = sf_get_post_meta( $post->ID, 'sf_thumbnail_link_url', true );
            $thumb_lightbox_thumb     = rwmb_meta( 'sf_thumbnail_image', 'type=image&size=large' );
            $thumb_lightbox_image     = rwmb_meta( 'sf_thumbnail_link_image', 'type=image&size=large' );
            $thumb_lightbox_video_url = sf_get_post_meta( $post->ID, 'sf_thumbnail_link_video_url', true );
            $thumb_lightbox_video_url = sf_get_embed_src( $thumb_lightbox_video_url );
            $permalink                = get_permalink();

            foreach ( $thumb_image as $detail_image ) {
                $thumb_img_url = $detail_image['url'];
                break;
            }

            if ( ! $thumb_image ) {
                $thumb_image   = get_post_thumbnail_id();
                $thumb_img_url = wp_get_attachment_url( $thumb_image, 'full' );
            }


            if ( $thumb_link_type == "link_to_url" ) {
                $link_config = 'href="' . $thumb_link_url . '" class="link-to-url"';
                $item_icon   = apply_filters( 'sf_port_url_icon', "ss-link" );
            } else if ( $thumb_link_type == "link_to_url_nw" ) {
                $link_config = 'href="' . $thumb_link_url . '" class="link-to-url" target="_blank"';
                $item_icon   = apply_filters( 'sf_port_url_icon', "ss-link" );
            } else if ( $thumb_link_type == "lightbox_thumb" ) {
                $link_config = 'href="' . $thumb_img_url . '" class="lightbox" data-rel="ilightbox[portfolio]"';
                $item_icon   = apply_filters( 'sf_port_lightbox_icon', "ss-view" );
            } else if ( $thumb_link_type == "lightbox_image" ) {
                $lightbox_image_url = '';
                foreach ( $thumb_lightbox_image as $image ) {
                    $lightbox_image_url = $image['full_url'];
                }
                $link_config = 'href="' . $lightbox_image_url . '" class="lightbox" data-rel="ilightbox[portfolio]"';
                $item_icon   = apply_filters( 'sf_port_lightbox_icon', "ss-view" );
            } else if ( $thumb_link_type == "lightbox_video" ) {
                $link_config = 'data-video="' . $thumb_lightbox_video_url . '" href="#" class="fw-video-link"';
                $item_icon   = apply_filters( 'sf_port_video_icon', "ss-video" );
            } else {
                $link_config = 'href="' . $permalink . '" class="link-to-post"';
                $item_icon   = apply_filters( 'sf_port_post_icon', "ss-navigateright" );
            }

            $item_link = array(
                "icon"   => $item_icon,
                "config" => $link_config
            );

            return $item_link;
        }
    }
?>