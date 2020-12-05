/* @*************************************************************************@
// @ @author Mansur Altamirov (Mansur_TL)									 @
// @ @author_url 1: https://www.instagram.com/mansur_tl                      @
// @ @author_url 2: http://codecanyon.net/user/mansur_tl                     @
// @ @author_email: highexpresstore@gmail.com                                @
// @*************************************************************************@
// @ ColibriSM - The Ultimate Modern Social Media Sharing Platform           @
// @ Copyright (c) 21.03.2020 ColibriSM. All rights reserved.                @
// @*************************************************************************@
*/

var pubbox_form_app_mixin = Object({
	data: function() {
		return {
			text: "",
			images: [],
			video: {},
			gifs_r1: [],
			gifs_r2: [],
			image_ctrl: true,
			video_ctrl: true,
			gif_ctrl: true,
			submitting: false,
			active_media: null,
			gif_source: null,
			og_imported: false,
			og_data: {},
			og_hidden: []
		};
	},
	computed: {
		valid_form: function() {
			var app = this;
			if (app.text.length && cl_empty(app.submitting)) {
				return true;
			}

			else if(app.images.length >= 1 && cl_empty(app.submitting)) {
				return true;
			}

			else if($.isEmptyObject(app.video) != true && cl_empty(app.submitting)) {
				return true;
			}

			else if(app.gif_source) {
				return true;
			}

			else if(app.og_imported) {
				return true;
			}

			return false;
		},
		equal_height: function() {
			var app_el = $(this.$el);

			return "{0}px".format((app_el.innerWidth() / 4));
		},
		preview_video: function() {
			if ($.isEmptyObject(this.video)) {
				return false;
			}

			return true;
		},
		gifs: function() {
			if (this.gifs_r1.length || this.gifs_r2.length) {
				return true;
			}

			return false;
		},
		show_og_data: function() {
			if (this.og_imported == true && this.active_media == null && this.og_hidden.contains(this.og_data.url) != true) {
				return true;
			}
			else {
				return false;
			}
		}
	},
	methods: {
		emoji_picker: function(action = "toggle") {
			var app_el = $(this.$el);
			var app    = this;
			if (app_el.length) {
				if (action == "toggle") {
					if (app_el.find('textarea#post-text').get(0).emojioneArea.isOpened()) {
						app_el.find('textarea#post-text').get(0).emojioneArea.hidePicker();
					}
					
					else {
						app_el.find('textarea#post-text').get(0).emojioneArea.showPicker();
						app.rep_emoji_picker();
					}
				}

				else if(action == "open") {
					if (app_el.find('textarea#post-text').get(0).emojioneArea.isOpened() != true) {
						app_el.find('textarea#post-text').get(0).emojioneArea.showPicker();
						app.rep_emoji_picker();
					}
				}

				else if(action == "close") {
					if (app_el.find('textarea#post-text').get(0).emojioneArea.isOpened()) {
						app_el.find('textarea#post-text').get(0).emojioneArea.hidePicker();
					}
				}
			}
		},
		rep_emoji_picker: function() {
			var app_el = $(this.$el);
			app_el.find('div.emojionearea-picker').css("top","{0}px".format(app_el.height() + 20));
		},
		textarea_resize: function(_self = null) {
			autosize($(_self.target));
		},
		publish: function(_self = null) {
			_self.preventDefault();

			var form         = $(_self.$el);
			var _app_        = this;
			var main_left_sb = $('div[data-app="left-sidebar"]');

			$(_self.target).ajaxSubmit({
				url: "<?php echo cl_link("native_api/main/publish_new_post"); ?>",
				type: 'POST',
				dataType: 'json',
				data: {
					gif_src: _app_.gif_source,
					thread_id: ((_app_.thread_id) ? _app_.thread_id : 0),
					curr_pn: SMColibri.curr_pn,
					og_data: _app_.og_data
				},
				beforeSend: function() {
					_app_.submitting = true;
				},
				success: function(data) {
					if (data.status == 200) {
						if (SMColibri.curr_pn == "home") {
							var home_timeline = $('div[data-app="homepage"]');
							var new_post      = $(data.html).addClass('animated fadeIn');

							if (home_timeline.find('div[data-an="entry-list"]').length) {
								home_timeline.find('div[data-an="entry-list"]').prepend(new_post).promise().done(function() {
									SMColibri.update_afterglow();

									delay(function() {
										new_post.removeClass('animated');
										new_post.removeClass('fadeIn');
									}, 1000);
								});
							}
							else {
								SMColibri.spa_reload();
							}
						}
						else if(SMColibri.curr_pn == "thread" && _app_.thread_id) {
							_app_.thread_id     = 0;
							var thread_timeline = $('div[data-app="thread"]');
							var new_post        = $(data.html).addClass('animated fadeIn');

							if(thread_timeline.find('div[data-an="replys-list"]').length) {
								thread_timeline.find('div[data-an="replys-list"]').prepend(new_post).promise().done(function() {
									SMColibri.update_afterglow();

									delay(function() {
										new_post.removeClass('animated');
										new_post.removeClass('fadeIn');
									}, 1000);
								});
							}
							else {
								SMColibri.spa_reload();
							}

							thread_timeline.find('[data-an="pub-replys-total"]').text(data.replys_total);
						}
						else {
							cl_bs_notify("<?php echo cl_translate('Your new publication has been posted on your timeline'); ?>", 1200);
						}

						if($(_app_.$el).attr('id') == 'vue-pubbox-app-2') {
							$(_app_.$el).parents("div#add_new_post").modal('hide');
						}

						if (data.posts_total) {
							main_left_sb.find('b[data-an="total-posts"]').text(data.posts_total);
						}
					}

					else {
						_app_.submitting = false;
						SMColibri.errorMSG();
					}
				},
				complete: function() {
					_app_.submitting = false;
					_app_.reset_data();

					SMColibri.update_afterglow();
				}
			});
		},
		select_images: function() {
			if (this.active_media == 'image' || cl_empty(this.active_media)) {
				if (this.image_ctrl) {
					var app_el = $(this.$el);
					app_el.find('input[data-an="images-input"]').trigger('click');
				}
			}
		},
		select_video: function() {
			if (cl_empty(this.active_media)) {
				if (this.video_ctrl) {
					var app_el = $(this.$el);
					app_el.find('input[data-an="video-input"]').trigger('click');
				}
			}
		},
		select_gifs: function() {
			var app  = this;
			var step = false;

			if (cl_empty(this.active_media)) {
				$.ajax({
					url: 'https://api.giphy.com/v1/gifs/trending',
					type: 'GET',
					dataType: 'json',
					data: {
						api_key: '{%config giphy_api_key%}',
						limit: 50,
						lang:'en',
						fmt:'json'
					},
				}).done(function(data) {
					if (data.meta.status == 200 && data.data.length > 0) {
						for (var i = 0; i < data.data.length; i++) {
							if (step) {
								app.gifs_r1.push({
									thumb: data['data'][i]['images']['preview_gif']['url'],
									src: data['data'][i]['images']['original']['url'],
								});
							}
							else {
								app.gifs_r2.push({
									thumb: data['data'][i]['images']['preview_gif']['url'],
									src: data['data'][i]['images']['original']['url'],
								});
							}

							step = !step;
						}
					}
				}).always(function() {
					if (app.gifs && cl_empty(app.active_media)) {
						app.active_media = "gifs";
					}

					app.disable_ctrls();
				});
			}
		},
		search_gifs: function(_self = null) {
			if (_self.target.value.length >= 2) {
				var query   = $.trim(_self.target.value);
				var step    = false;
				var app     = this;
				var gifs_r1 = app.gifs_r1;
				var gifs_r2 = app.gifs_r2;

				$.ajax({
					url: 'https://api.giphy.com/v1/gifs/search',
					type: 'GET',
					dataType: 'json',
					data: {
						q: query,
						api_key:'{%config giphy_api_key%}',
						limit: 50,
						lang:'en',
						fmt:'json'
					}
				}).done(function(data) {
					if (data.meta.status == 200 && data.data.length > 0) {
						app.gifs_r1 = [];
						app.gifs_r2 = [];

						for (var i = 0; i < data.data.length; i++) {
							if (step) {
								app.gifs_r1.push({
									thumb: data['data'][i]['images']['preview_gif']['url'],
									src: data['data'][i]['images']['original']['url'],
								});
							}
							else {
								app.gifs_r2.push({
									thumb: data['data'][i]['images']['preview_gif']['url'],
									src: data['data'][i]['images']['original']['url'],
								});
							}

							step = !step;
						}
					}
					else {
						app.gifs_r1 = gifs_r1;
						app.gifs_r2 = gifs_r2;
					}
				});
			}
		},
		preview_gif: function(_self = null) {
			if (_self.target) {
				this.gif_source = $(_self.target).data('source');
			}
		},
		rm_preview_gif: function() {
			this.gif_source = null;
		},
		close_gifs: function() {
			var app          = this;
			app.gifs_r1      = [];
			app.gifs_r2      = [];
			app.active_media = null;
			app.disable_ctrls();
		},
		rm_gif_preloader(_self = null) {
			if (_self.target) {
				$(_self.target).siblings('div').remove();
				$(_self.target).parent('div').removeClass('loading');
			}
		},
		upload_images: function(event = null) {
			var app    = this;
			var app_el = $(app.$el);

			if (cl_empty(app.active_media) || app.active_media == 'image') {
				var images = event.target.files;

				if (SMColibri.curr_pn == 'thread') {
	        		$('div[data-app="modal-pubbox"]').addClass('vis-hidden');
	        	}

				SMColibri.upload_progress_bar('show', "<?php echo cl_translate('Uploading images'); ?>");

				if (images.length) {
					for (var i = 0; i < images.length; i++) {
						var form_data  = new FormData();
						var break_loop = false;
						form_data.append('delay', 1);
						form_data.append('image', images[i]);
						form_data.append('hash', "<?php echo fetch_or_get($cl['csrf_token'],'none'); ?>");
						
						$.ajax({
							url: '<?php echo cl_link("native_api/main/upload_post_image"); ?>',
							type: 'POST',
							dataType: 'json',
							enctype: 'multipart/form-data',
							data: form_data,
							cache: false,
					        contentType: false,
					        processData: false,
					        timeout: 600000,
					        beforeSend: function() {
					        	app.submitting = true;
					        },
							success: function(data) {
								if (data.status == 200) {
									app.images.push(data.img);
								}
								else if(data.err_code == "total_limit_exceeded") {
									cl_bs_notify("<?php echo cl_translate('You cannot attach more than 10 images to this post.'); ?>",1500);
									break_loop = true;
								}
								else {
									cl_bs_notify("<?php echo cl_translate('An error occurred while processing your request. Please try again later.'); ?>",3000);
								}
							},
							complete: function() {
								if (app.images.length && cl_empty(app.active_media)) {
									app.active_media = "image";
								}

								app.disable_ctrls();

								app.submitting = false;
							}
						});

						if (break_loop) {break;}
					}
				}

				setTimeout(function() {
					SMColibri.upload_progress_bar('end');

					if (SMColibri.curr_pn == 'thread') {
		        		$('div[data-app="modal-pubbox"]').removeClass('vis-hidden');
		        	}
				}, 1500);

				app_el.find('input[data-an="images-input"]').val('');
			}
		},
		upload_video: function(event = null) {
			var app    = this;
			var app_el = $(app.$el);

			if (cl_empty(app.active_media)) {
				var video  = event.target.files[0];
				if (video) {
					var form_data = new FormData();
					form_data.append('video', video);
					form_data.append('hash', "<?php echo fetch_or_get($cl['csrf_token'],'none'); ?>");

					$.ajax({
						url: '<?php echo cl_link("native_api/main/upload_post_video"); ?>',
						type: 'POST',
						dataType: 'json',
						enctype: 'multipart/form-data',
						data: form_data,
						cache: false,
				        contentType: false,
				        processData: false,
				        timeout: 600000,
				        beforeSend: function() {
				        	SMColibri.upload_progress_bar('show', "<?php echo cl_translate('Uploading video'); ?>");

				        	if (SMColibri.curr_pn == 'thread') {
				        		$('div[data-app="modal-pubbox"]').addClass('vis-hidden');
				        	}
				        },
						success: function(data) {
							if (data.status == 200) {
								app.video = data.video;
							}
							else if(data.err_code == "total_limit_exceeded") {
								cl_bs_notify("<?php echo cl_translate('You cannot attach more than 1 video to this post.'); ?>",1500);
							}
							else {
								cl_bs_notify("<?php echo cl_translate('An error occurred while processing your request. Please try again later.'); ?>",3000);
							}
						},
						complete: function() {
							if ($.isEmptyObject(app.video) != true && cl_empty(app.active_media)) {
								app.active_media = "video";
							}

							app.disable_ctrls();
							app_el.find('input[data-an="video-input"]').val('');

							setTimeout(function() {
								SMColibri.upload_progress_bar('end');

								if (SMColibri.curr_pn == 'thread') {
					        		$('div[data-app="modal-pubbox"]').removeClass('vis-hidden');
					        	}
							}, 1500);
						}
					});
				}
			}
		},
		delete_image: function(id = null) {
			if (cl_empty(id)) {
				return false;
			}

			else {
				var app = this;
				for (var i = 0; i < app.images.length; i++) {
					if (app.images[i]['id'] == id) {
						app.images.splice(i, 1);
					}
				}

				$.ajax({
					url: '<?php echo cl_link("native_api/main/delete_post_image"); ?>',
					type: 'POST',
					dataType: 'json',
					data: {image_id: id},
				}).done(function(data) {
					if (data.status != 200) {
						cl_bs_notify("<?php echo cl_translate("An error occurred while processing your request. Please try again later."); ?>",3000);
					}
				}).always(function() {
					if (cl_empty(app.images.length)) {
						app.active_media = null;
					}

					app.disable_ctrls();
				});
			}
		},
		delete_video: function() {
			var app = this;
			$.ajax({
				url: '<?php echo cl_link("native_api/main/delete_post_video"); ?>',
				type: 'POST',
				dataType: 'json',
			}).done(function(data) {
				if (data.status != 200) {
					cl_bs_notify("<?php echo cl_translate("An error occurred while processing your request. Please try again later."); ?>",3000);
				}
				else {
					app.video = Object({});
				}
			}).always(function() {
				if ($.isEmptyObject(app.video)) {
					app.active_media = null;
				}

				app.disable_ctrls();
			});
		},
		disable_ctrls: function() {
			if (this.active_media == 'image' && this.images.length >= 10) {
				this.image_ctrl = false;
				this.gif_ctrl   = false;
				this.video_ctrl = false;
			}
			else if(this.active_media == 'image' && this.images.length < 10) {
				this.image_ctrl = true;
				this.gif_ctrl   = false;
				this.video_ctrl = false;
			}
			else if(this.active_media != null) {
				this.image_ctrl = false;
				this.gif_ctrl   = false;
				this.video_ctrl = false;
			}
			else {
				this.image_ctrl = true;
				this.gif_ctrl   = true;
				this.video_ctrl = true;
			}
		},
		reset_data: function() {
			this.image_ctrl   = true;
			this.gif_ctrl     = true;
			this.video_ctrl   = true;
			this.og_imported  = false;
			this.text         = "";
			this.images       = [];
			this.video        = {};
			this.og_data      = {};
			this.active_media = null;
			this.gif_source   = null;
			this.gifs_r1      = [];
			this.gifs_r2      = [];
			this.og_hidden    = [];
			$(this.$el).find('textarea#post-text').get(0).emojioneArea.setText("");
			this.rep_emoji_picker();
		},
		rm_preview_og: function() {
			var _app_         = this;
			_app_.og_hidden.push(_app_.og_data.url);

			_app_.og_imported = false;
			_app_.og_data     = Object({});
		}
	},
	updated: function() {
		var _app_ = this;

		if ($.isEmptyObject(_app_.video) != true) {
			SMColibri.update_afterglow();
		}

		_app_.rep_emoji_picker();

		delay(function() {
			if (_app_.og_imported != true) {
				var text_links = _app_.text.match(/(https?:\/\/[^\s]+)/);

				if (text_links && text_links.length > 0 && _app_.og_hidden.contains(text_links[0]) != true) {
					$.ajax({
						url: '<?php echo cl_link("native_api/main/import_og_data"); ?>',
						type: 'POST',
						dataType: 'json',
						data: {
							url: text_links[0]
						}
					}).done(function(data) {
						if (data.status == 200) {
							_app_.og_imported = true;
							_app_.og_data     = data.og_data;
						}
					});
				}
			}
		}, 800);
	},
	mounted: function() {
		if ($.isEmptyObject(this.video) != true) {
			SMColibri.update_afterglow();
		}

		<?php if (not_empty($me['draft_post'])): ?>
			if ($(this.$el).attr('id') == 'vue-pubbox-app-1') {
				var app = this;
				delay(function() {
					$.ajax({
						url: '<?php echo cl_link("native_api/main/get_draft_post"); ?>',
						type: 'GET',
						dataType: 'json'
					}).done(function(data) {
						if (data.status == 200 && data.type == "image") {
							app.images       = data.images;
							app.active_media = 'image';
						}
						else if(data.status == 200 && data.type == "video") {
							app.video        = data.video;
							app.active_media = 'video';
						}
						else {
							return false;
						}

						if (data.status == 200) {
							cl_bs_notify("<?php echo cl_translate('Please finish editing the post or delete media files!'); ?>",3000);
						}
					}).always(function() {
						app.disable_ctrls();
					});
				}, 1500);
			}
		<?php endif; ?>
	}
});

var emoji_filters = Object({
	recent: {
		title: "<?php echo cl_translate("Recent"); ?>"
	},
	smileys_people: {
        title: "<?php echo cl_translate("Emoticons and People"); ?>",
    },
	animals_nature: {
        title: "<?php echo cl_translate("Animals & Nature"); ?>",
    },
	food_drink: {
        title: "<?php echo cl_translate("Food & Drink"); ?>",
    },
	activity: {
        title: "<?php echo cl_translate("Activity"); ?>",
    },
	travel_places: {
        title: "<?php echo cl_translate("Travel & Places"); ?>",
    },
	objects: {
        title: "<?php echo cl_translate("Objects"); ?>",
    },
	symbols: {
        title: "<?php echo cl_translate("Symbols"); ?>",
    },
	flags: {
        title: "<?php echo cl_translate("Flags"); ?>",
    }
});