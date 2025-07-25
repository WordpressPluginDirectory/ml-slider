<template>

	<!-- This component will work similar to the featured image component -->
	<section
		v-if="current.id"
		:class="{'unsupported': unsupportedSliderType}"
		class="theme-select-module">
		<p v-if="hasThemeSet">
			{{ __('Slideshow Theme', 'ml-slider') }}: 
				<template v-if="'custom' == current.theme.type">
					<span v-if="current.theme.version === 'v2'">
						{{ current.theme.base_title }}
					</span>
					<span v-else>
						{{ current.theme.title }}
					</span>
				</template>
				<template v-else>
					<span>{{ current.theme.title }}</span>
				</template>
		</p>
		<div
			:class="{'ms-modal-open': is_open}"
			class="inside wp-clearfix metaslider-theme-viewer">

			<!-- If the theme is not supported we should show an error -->
			<p
				v-if="(hasThemeSet && unsupportedSliderType)"
				class="slider-not-supported-warning">
				{{ __('This theme was designed for FlexSlider. Please choose the FlexSlider option for the best display.', 'ml-slider') }}
			</p>

			<!-- If there's a theme already set -->
			<div
				v-if="hasThemeSet"
				class="ms-current-theme">
				<button
					style="width:100%;text-decoration:none"
					type="button"
					class="button-link change-theme-img-button"
					@click="openModal">
					<div
						v-if="'custom' == current.theme.type"
						class="custom-theme-single p-0">
						<template v-if="current.theme.version === 'v2'">
							<div class="theme-label-info-v2">
								<div class="custom-subtitle">
									{{ current.theme.base_title + ' ' + __('theme', 'ml-slider') }}
								</div>
								{{ current.theme.title }}
							</div>
							<div class="theme-image-wrapper">
								<img 
									:src="themeDirectoryUrl + current.theme.base + '/screenshot.png'"
									:alt="current.theme.title"
									class="theme-image-v2"> 
							</div>
						</template>
						<template v-else>
							<div class="theme-label-info-legacy">
								{{ __('Legacy', 'ml-slider') }}
							</div>
							<div class="custom-theme-single">
								<div class="custom-subtitle">
									{{ __('Custom theme', 'ml-slider') }}
								</div>
								{{ current.theme.title }}
							</div>
						</template>
					</div>
					<div v-else>
						<img
							v-if="current.theme.screenshot_dir"
							:src="current.theme.screenshot_dir + '/screenshot.png'"
							:alt="current.theme.title">
						<img
							v-else
							:src="themeDirectoryUrl + current.theme.folder + '/screenshot.png'"
							:alt="current.theme.title">
					</div>
				</button>
				<button
					type="button"
					class="button-link remove-theme"
					@click="removeTheme">{{ __('Remove', 'ml-slider') }}
				</button>
				<button
					type="button"
					class="button-link change-theme"
					@click="openModal">{{ __('Change', 'ml-slider') }}
				</button>
				<!-- Customize theme design (optional) -->
				<theme-customize :manifest="theme_customize"></theme-customize>
			</div>

			<!-- If no theme then we render the theme select button -->
			<div v-else>
				<p>
					{{ __('Change the design of your slideshow with a stylish MetaSlider theme!', 'ml-slider') }}
				</p>
				<button
					v-if="Object.keys(themes).length || Object.keys(customThemes).length"
					type="button"
					class="button"
					@click="openModal">{{ __('Select a custom theme', 'ml-slider') }}
				</button>
			</div>

			<!-- This will be a modal for showing the themes -->
			<sweet-modal
				ref="themesModal"
				:hide-close-button="true"
				:blocking="true"
				:pulse-on-block="false"
				overlay-theme="dark"
				@close="is_open = false">
				<button
					slot="box-action"
					@click.prevent="$refs.themesModal.close()">
                    <svg class="w-6 -mt-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
				</button>
				<sweet-modal-tab
					id="all"
					:title="__('All themes', 'ml-slider')">
					<div v-if="loading || loadingCustom">
						{{ __('Loading...', 'ml-slider') }}
					</div>
					<template v-if="(themes && Object.keys(themes).length) || (Object.keys(customThemes).length && proUser)">
						<div class="columns">
							<div class="theme-list-column">
								<ul class="ms-image-selector regular-themes">
									<li
										v-if="themes && Object.keys(themes).length"
										v-for="theme in themes"
										:key="theme.folder"
										:class="{ 
											'a-theme': true, selected: (selectedTheme.folder === theme.folder), 
											'unlock-pro-theme-ad': nonSelectablePremiumTheme(theme.type)
										}"
										role="checkbox"
										@mouseover="hoveredTheme = theme; showPremiumThemeAd(theme.type, theme.folder)"
										@mouseout="hoveredTheme = selectedTheme"
										@mouseleave="hidePremiumThemeAd(theme.type)"
										@click="nonSelectablePremiumTheme(theme.type) ? null : selectTheme(theme)">
										<span>
											<div 
												v-if="revealThemeAd === theme.folder"
												class="custom-theme-single upgrade-pro-theme-ad">
												<h3 class="text-white mb-3">{{ __('Get MetaSlider Pro!', 'ml-slider') }}</h3>
												<p class="text-white font-normal text-sm mb-3">
													{{ __('Upgrade now to unlock this theme!', 'ml-slider') }}
												</p>
												<a class="w-full inline-flex items-center justify-center px-5 py-2 border border-transparent rounded-md text-white bg-orange hover:bg-orange-darker active:bg-orange-darkest transition ease-in-out duration-150 md:w-auto text-base" :href="hoplink" target="_blank">{{ __('Upgrade now', 'ml-slider') }} <span class="dashicons dashicons-external border-0"></span></a>
											</div>
											<img
												v-if="theme.screenshot_dir"
												:src="theme.screenshot_dir + '/screenshot.png'"
												:alt="theme.title">
											<img
												v-else
												:src="themeDirectoryUrl + theme.folder + '/screenshot.png'"
												:alt="theme.title">
										</span>
									</li>

									<template v-if="Object.keys(customThemes).length && proUser">
										<li
											v-for="theme in customThemes"
											:key="theme.folder"
											:class="{ 'a-theme': true, selected: (selectedTheme.folder == theme.folder) }"
											role="checkbox"
											@mouseover="hoveredTheme = theme"
											@mouseout="hoveredTheme = selectedTheme"
											@click="selectTheme(theme)">
											<span>
												<template v-if="theme.version === 'v2'">
													<div class="theme-label-info-v2">
														<div class="custom-subtitle">
															{{ theme.base_title + ' ' + __('theme', 'ml-slider') }}
														</div>
														{{ theme.title }}
													</div>
													<div class="theme-image-wrapper">
														<img 
															:src="themeDirectoryUrl + theme.base + '/screenshot.png'"
															:alt="theme.title"
															class="theme-image-v2"> 
													</div>
												</template>
												<template v-else>
													<div class="theme-label-info-legacy">
														{{ __('Legacy', 'ml-slider') }}
													</div>
													<div class="custom-theme-single">
														{{ theme.title }}
													</div>
												</template>
											</span>
										</li>
									</template>
									<template v-else-if="!Object.keys(customThemes).length && proUser && !loadingCustom">
										<li class="a-theme">
											<span>
												<div class="custom-theme-single upgrade-pro-theme-ad">
													<h3 class="text-white mb-3">{{ __('MetaSlider Pro is installed!', 'ml-slider') }}</h3>
													<p class="text-white font-normal text-sm mb-3">
														{{ __('You can create your own themes with our theme editor', 'ml-slider') }}
													</p>
													<a class="w-full inline-flex items-center justify-center px-5 py-2 border border-transparent rounded-md text-white bg-orange hover:bg-orange-darker active:bg-orange-darkest transition ease-in-out duration-150 md:w-auto text-base" :href="themeEditorLink">{{ __('Get started', 'ml-slider') }}</a>
												</div>
											</span>
										</li>
									</template>
									<template v-else>
										<li class="a-theme unlock-pro-custom-themes-ad">
											<span>
												<div class="custom-theme-single upgrade-pro-theme-ad">
													<h3 class="text-white mb-3">{{ __('Get MetaSlider Pro!', 'ml-slider') }}</h3>
													<p class="text-white font-normal text-sm mb-3">
														{{ __('Upgrade now to build your own custom themes!', 'ml-slider') }}
													</p>
													<a class="w-full inline-flex items-center justify-center px-5 py-2 border border-transparent rounded-md text-white bg-orange hover:bg-orange-darker active:bg-orange-darkest transition ease-in-out duration-150 md:w-auto text-base" :href="hoplink" target="_blank">{{ __('Upgrade now', 'ml-slider') }} <span class="dashicons dashicons-external border-0"></span></a>
												</div>
											</span>
										</li>
									</template>
								</ul>
							</div>
							<div class="theme-details-column">
								<template v-if="showThemeDetails && hoveredTheme.type !== 'custom'">
									<div>
										<h1
											slot="button"
											class="metaslider-theme-title"
											v-text="hoveredTheme.title"/>
										<template v-if="hoveredTheme.description">
											<div class="ms-theme-description">
												<h2>{{ __('Theme Details', 'ml-slider') }}</h2>
												<p v-html="hoveredTheme.description"/>
											</div>
										</template>
										<template v-if="hoveredTheme.instructions">
											<div class="ms-theme-instructions">
												<h2>{{ __('Theme Instructions', 'ml-slider') }}</h2>
												<p v-html="hoveredTheme.instructions"/>
											</div>
										</template>
									</div>
									<div v-if="hoveredTheme && hoveredTheme.tags && hoveredTheme.tags.length">
										<h3>{{ __('Tags', 'ml-slider') }}</h3>
										<ul class="ms-theme-tags">
											<li
												v-for="(tag, i) in hoveredTheme.tags"
												:key="i"
												v-text="tag"/>
										</ul>
									</div>
								</template>
								<template v-else-if="hoveredTheme.type === 'custom'">
									<div>
										<h1 class="metaslider-theme-title">
											 <template v-if="hoveredTheme.version === 'v2'">
												{{ hoveredTheme.base_title }}
											 </template>
											<template v-else>
												{{ hoveredTheme.title }}
											</template>
										</h1>
										<div class="ms-theme-description">
											<template v-if="hoveredTheme.version === 'v2'">
												<h2>{{ __('Style Details', 'ml-slider') }}</h2>
												<p>{{ __('This style was created with the Theme Editor.', 'ml-slider') }}</p>
											</template>
											<template v-else>
												<h2>{{ __('Theme Details', 'ml-slider') }}</h2>
												<p>{{ __('This theme was created through the theme editor.', 'ml-slider') }}</p>
											</template>
										</div>
									</div>
								</template>
								<template v-else>
									<template v-if="proUser">
										<div>
											<div>
												<h1 class="metaslider-theme-title">{{ __('How To Use', 'ml-slider') }}</h1>
												<p>{{ __('Select a theme on the left to use on this slideshow. Click the theme for more details.', 'ml-slider') }}</p>
											</div>
										</div>
									</template>
									<template v-else>
										<div>
											<h1 class="metaslider-theme-title">{{ __('Get MetaSlider Pro!', 'ml-slider') }}</h1>
											<p>{{ __('MetaSlider Pro gives you access to extra themes. You can also create completely new themes that can easily be added to new slideshows.', 'ml-slider') }}</p>
										</div>
									</template>
								</template>
							</div>
						</div>
					</template>
					<template v-else>
						<div class="free-themes-not-found">
							<h1>{{ __('Error: No themes were found.', 'ml-slider') }}</h1>
						</div>
					</template>
				</sweet-modal-tab>
				<template
					slot="button">
					<div>
						<span
							v-if="sliderTypeNotSupported"
							class="slider-not-supported-warning">
							{{ __('This theme was designed for FlexSlider. Please choose the FlexSlider option for the best display.', 'ml-slider') }}</span>
					</div>
					<div class="flex items-center">
						<button
							:title="__('Preview slideshow', 'ml-slider')"
							class="flex items-center m-0 mr-1 text-gray-darker"
							@click.prevent="openPreview">
                            <svg class="w-6 inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            {{ __('Preview', 'ml-slider') }}
						</button>
						<button
							:disabled="!selectedTheme.folder"
							class="button button-primary"
							@click.stop.prevent="setTheme">{{ __('Select', 'ml-slider') }}
						</button>
					</div>
				</template>
			</sweet-modal>
		</div>
	</section>
</template>

<script>
import { EventManager } from '../utils'
import { Axios } from '../api'
import './components'
import { mapGetters } from 'vuex'
import QS from 'qs'
import { default as ThemeCustomize } from './includes/ThemeCustomize'

export default {
	components: {
		'theme-customize' : ThemeCustomize
	},
	props: {
		themeDirectoryUrl: {
			type: [String],
			default: ''
		}
	},
	data() {
		return {
			loading: true,
			loadingCustom: true,
			unsupportedSliderType: false,
			themes: {},
			customThemes: {},
			selectedTheme: {},
			hoveredTheme: {},
			is_open: false,
			revealThemeAd: null,
			theme_customize: [], // @TODO Maybe declare as {} ?
			theme_edit_settings: {}
		}
	},
	watch: {
		currentThemeSupports() {
			// TODO: Settings - once reactive, refactor this
			this.updateSupportedStatus()
		},
		current: {
			immediate: true,
			handler: function(current) {
				// hoveredTheme controls what shows in the sidebar
				if (!this.current || !this.current.theme || 'none' === this.current.theme) {
					this.selectedTheme = this.hoveredTheme = this.themeStub()
					return
				}
				this.selectedTheme = this.current.theme
				this.hoveredTheme = this.current.theme

				// Get the theme customizations if available from db to avoid sync issues
				Axios.get('theme/customization', {
					params: {
						action: 'ms_get_theme_customization',
						slideshow_id: this.current.id,
						theme: this.selectedTheme['folder'], // Just the folder!
						type: this.selectedTheme['type'] // free, premium, etc.
					}
				}).then(response => {
					var customize_data = response.data.data;
					
					// Assign defaults from manifest, including default values
					this.theme_customize = customize_data.manifest || [];

					// Iterate each section that has 'status' key
					this.theme_customize.forEach((section_item, section_index) => {

						// Loop each section 'settings' key
						section_item.settings.forEach((row_item, row_index) => {
							if (row_item.type === 'fields' && typeof row_item.fields !== 'undefined') {
								// Replace default values with the saved ones
								for (let i = 0; i < row_item.fields.length; i++) {
									// The 'name' in theme_customize should match keys in saved_settings
									const name = row_item.fields[i].name; 
									const manifest_fields = this.theme_customize[section_index].settings[row_index].fields[i];

									// Check if saved_settings contains the key matching 'name'
									if (customize_data.saved_settings 
										&& customize_data.saved_settings.hasOwnProperty(name)
										&& typeof customize_data.saved_settings[name] !== 'undefined') {
											manifest_fields.value = customize_data.saved_settings[name];
									} else {
										// Use default value if saved setting for this name doesn't exist
										manifest_fields.value = manifest_fields.default;
									}
								}
							} else {
								// The 'name' in theme_customize should match keys in saved_settings
								const name = row_item.name;
								const manifest_data = this.theme_customize[section_index].settings[row_index];

								// Check if saved_settings contains the key matching 'name'
								if (customize_data.saved_settings 
									&& customize_data.saved_settings.hasOwnProperty(name)
									&& typeof customize_data.saved_settings[name] !== 'undefined') {
									manifest_data.value = customize_data.saved_settings[name];
								} else {
									// Use default value if saved setting for this name doesn't exist
									manifest_data.value = manifest_data.default;
								}
							}
						});
					});

					this.updateColorPicker();
				}).catch(error => {
					this.notifyError('metaslider/theme-error', error, true)
				})
			}
		}
	},
	computed: {
		showThemeDetails() {
			return this.hoveredTheme.description || (this.selectedTheme.description && !this.isCustomTheme)
		},
		isCustomTheme() {
			if (!this.selectedTheme) return false
			return this.selectedTheme && this.selectedTheme.folder ? this.selectedTheme.folder.startsWith('_theme') : false
		},
		sliderTypeNotSupported() {
			if (!this.hovererdTheme || !this.hoveredTheme.tags) {
				return false
			}

			// TODO: Settings - once reactive, refactor this
			let currentType = document.querySelector('input[name="settings[type]"]:checked')
			if (!currentType) return false
			return parseInt(this.hoveredTheme.supports.indexOf(currentType.value), 10) === -1
		},
		supportLink() {
			return this.proUser ? 'https://www.metaslider.com/support/' : 'https://wordpress.org/support/plugin/ml-slider'
		},
		currentThemeSupports() {
			if (!this.current.id) return undefined
			return this.current.theme ? this.current.theme.supports : undefined
		},
		hasThemeSet() {
			if (!this.current.id || !this.current.hasOwnProperty('theme')) return false
			return this.current.theme.hasOwnProperty('folder') && this.current.theme.folder.length
		},
		...mapGetters({
			current: 'slideshows/getCurrent'
		})
	},
	created() {},
	mounted() {
		this.fetchThemes()

		// TODO: when converting settings to vue, this could be removed
		document.querySelectorAll('input[name="settings[type]"]').forEach(sliderType => {
			sliderType.addEventListener('click', event => {

				// TODO: Settings - once reactive, refactor this
				this.updateSupportedStatus()

				// hack to work with non-vue (refreshes computed properties)
				this.hoveredTheme = {}
				this.hoveredTheme = this.selectedTheme || this.current.theme
			})
		})

		this.updateSupportedStatus()
		this.setColorPicker();
	},
	methods: {
		fetchThemes() {

			// Pre-built themes
			Axios.get('themes/all', {
				params: {
					action: 'ms_get_all_free_themes'
				}
			}).then(response => {
				this.themes = response.data.data
				this.loading = false
			}).catch(error => {
				this.loading = false
				this.notifyError('metaslider/theme-error', error, true)
			})

            // Custom themes
            this.loadingCustom = this.proUser
			this.proUser && Axios.get('themes/custom', {
				params: {
					action: 'ms_get_custom_themes'
				}
			}).then(response => {
				this.customThemes = (typeof response.data.data === 'object') ? response.data.data : {}
				this.loadingCustom = false
			}).catch(error => {
				this.loadingCustom = false
				this.notifyError('metaslider/theme-error', error, true)
			})
		},
		selectTheme(theme) {
			this.selectedTheme = (this.selectedTheme === theme) ? {} : theme
		},
		removeTheme() {
			this.selectedTheme = {}
			this.setTheme()
		},
		setTheme() {
			this.notifyInfo('metaslider/theme-updating', this.__('Saving theme...', 'ml-slider'))
			this.$refs.themesModal.close()

			// If the selected theme is set and already the current theme, do nothing
			if (Object.keys(this.selectedTheme).length && Object.is(this.selectedTheme.folder, this.current.theme.folder)) {
				this.notifySuccess('metaslider/theme-updated', this.__('Theme saved', 'ml-slider'), true)
			} else {
				this.$store.commit('slideshows/updateTheme', this.selectedTheme)

				Axios.post('themes/set', QS.stringify({
					action: 'ms_set_theme',
					slideshow_id: this.current.id,
					theme: this.selectedTheme
				})).then(response => {
					this.theme_customize = this.selectedTheme.customize || [];

					// Iterate each section that has 'status' key
					this.theme_customize.forEach((section_item, section_index) => {
						
						// Loop each section 'settings' key
						section_item.settings.forEach((row_item, row_index) => {

							if (row_item.type === 'fields' && typeof row_item.fields !== 'undefined') {
								// Add value property by copying default property value
								for (let i = 0; i < row_item.fields.length; i++) {
									const manifest_fields = this.theme_customize[section_index].settings[row_index].fields[i];

									manifest_fields.value = manifest_fields.default;
								}
							} else {
								const manifest_data = this.theme_customize[section_index].settings[row_index];

								manifest_data.value = manifest_data.default;
							}
						});
					});

					this.updateColorPicker();

					setTimeout(() => {
						// @TODO - Maybe move to admin.js under window.metaslider.app.EventManager.$on("metaslider/theme-updated", function () {});
						this.showHideColorPicker();  //delay to load all picker first
					}, 1000);

					this.notifySuccess('metaslider/theme-updated', this.__('Theme saved', 'ml-slider'), true)

					if (typeof metaslider.autoThemeConfig !== 'undefined' && parseInt(metaslider.autoThemeConfig, 10)) {
						this.theme_edit_settings = this.selectedTheme.edit_settings ?? {};
						this.updateEditSettings();
					}
				}).catch(error => {
					this.notifyError('metaslider/theme-error', error, true)
				})
			}
		},
		setColorPicker() {
			var $ = window.jQuery;
			$('.static-theme-customize .colorpicker').each(function () {
				$(this).wpColorPicker({
					change: function(event, ui) {
						var input = $(this).parents('.wp-picker-container').find('input.colorpicker');
						var btn = $(this).parents('.wp-picker-container').find('button.wp-color-result');
			
						btn.css('background-color',ui.color.toCSS('rgba'));
			
						input.data('new-color',ui.color.toCSS('rgba'));
						input.attr('value',ui.color.toCSS('rgba'));
			
						btn.trigger('change');
					}
				}).promise().done(function() {
					var text = typeof metaslider !== 'undefined' ? metaslider : null;
					if (text) {
						$(this).parents('.wp-picker-container').find('.iris-strip').eq(0).prepend(`<span class="ms-color-tooltip">${text.tone}</span>`);
						$(this).parents('.wp-picker-container').find('.iris-strip').eq(1).prepend(`<span class="ms-color-tooltip">${text.opacity}</span>`);
					}
				});
			});
		},
		updateColorPicker() {
			this.$nextTick( function () {
				this.setColorPicker();

				var $ = window.jQuery;
				$('.static-theme-customize .colorpicker').each(function () {
					const newColor = $(this).val();
					if (newColor.length) {
						$(this).wpColorPicker('color', newColor);
					}
				});
			});
		},
		updateEditSettings() {
			this.$nextTick( function () {

				if (Object.keys(this.theme_edit_settings).length > 0) {
					var $ = window.jQuery;

					for (const [key,value] of Object.entries(this.theme_edit_settings)) {
						const field = $(`#metaslider_configuration [name="settings[${key}]"]`);

						if (field.length == 1) {
							if (field.is('select')) {
								// select
								if (field.find(`option[value="${value}"]`).length) {
									field.val(value).trigger('change');
								}
							} else if (field.is(':checkbox')) {
								// checkbox
								field.prop('checked', value).trigger('change');
							} else if (field.is('input')) {
								// input
								const fieldType = field.attr('type');
								if (fieldType === 'text' || fieldType === 'number') {
									field.val(value).trigger('change');
								}
							}
							field.attr('data-edit-setting', true); // Not requied. We add it just for reference
						}
					}

					setTimeout(function () {
						EventManager.$emit('metaslider/save');
					}, 1000);
				}
			});
		},
		openModal() {
			// TODO: when converting settings to vue, this could be removed.
			// It's used to force re-render of the UI
			this.hoveredTheme = this.selectedTheme || this.current.theme

			// If a current theme is selected, show that tab
			let tab = 'all'
			this.is_open = true
			this.$refs.themesModal.open(tab)
		},
		openPreview() {
			EventManager.$emit('metaslider/preview', {
				slideshowId: this.current.id,
				themeId: this.selectedTheme ? this.selectedTheme.folder : ''
			})
		},
		updateSupportedStatus() {
			if (!this.current.id || 'undefined' === typeof this.currentThemeSupports) return true
			let currentType = document.querySelector('input[name="settings[type]"]:checked')
			this.unsupportedSliderType = this.currentThemeSupports ? this.currentThemeSupports.indexOf(currentType.value) === -1 : false
		},
		themeStub() {
			return {
				description: null,
				folder: null,
				images: [],
				supports: [],
				tags: [],
				title: null,
				type: null
			}
		},
		showPremiumThemeAd(type, id) {
			if (this.nonSelectablePremiumTheme(type)) {
				this.revealThemeAd = id;
			}
		},
		hidePremiumThemeAd(type) {
			if (this.nonSelectablePremiumTheme(type)) {
				this.revealThemeAd = null;
			}
		},
		nonSelectablePremiumTheme(type) {
			return !this.proUser && type === 'premium';
		},
		showHideColorPicker() {
			this.$nextTick( function () {
				var $ = window.jQuery;
				$('.static-theme-customize tr').show();
				if ($('.ms-settings-table input[name="settings[pausePlay]"]').is(':checked')) {
					$('tr.customizer-pausePlay').show();
				} else {
					$('tr.customizer-pausePlay').hide();
				}
				if ($('.ms-settings-table select[name="settings[links]"]').val() === 'false') {
					$('tr.customizer-links').hide();
				} else {
					$('tr.customizer-links').show();
				}

				if ($('.ms-settings-table select[name="settings[navigation]"]').val() === 'true') {
					$('tr.customizer-navigation').show();
				} else {
					$('tr.customizer-navigation').hide();
				}
			});
		}
	}
}
</script>

<style lang="scss">
	@import '../assets/styles/globals.scss';
	@import '../assets/styles/mixins.scss';

	@mixin custom-theme-box() {
		.theme-image-wrapper {
			background: #2271b1;
			width: 100%;
			height: 100%;
			display: block;
		}
		.theme-label-info-v2 {
			position: absolute;
			top: 50%;
			left: 0;
			color: #fff;
			font-size: 1.3rem;
			font-weight: bold;
			z-index: 1;
			text-shadow: 0 0px 10px #000;
			width: 100%;
			transform: translateY(-50%);
			text-align: center;
			padding-left: 1rem;
			padding-right: 1rem;

			.custom-subtitle {
				color: #fff;
				font-size: 12px;
				font-weight: 300;
				margin-bottom: .1em;
				text-transform: uppercase;
			}
		}
		.theme-image-v2 {
			opacity: 0.6;
		}
		.theme-label-info-legacy {
			position: absolute;
			top: 10px;
			right: 10px;
			background: rgba(255,255,255,1);
			color: #2271b1;
			font-size: 0.7em;
			font-weight: normal;
			padding: 3px 7px;
			border-radius: 4px;
			z-index: 1;
			opacity: 0.5;
		}
	}
	#metaslider-ui .metaslider-theme-viewer {
		p {
			margin-top: 0;
			color: #444;
		}
	}
	#metaslider-ui .metaslider-theme-viewer > .sweet-modal-overlay > .sweet-modal {
		position: absolute;
		display: flex;
		flex-direction: column;
		width: 100%;
		height: 100%;
		max-width: 90%;
		max-height: 90%;
		left: 5%;
		top: 5%;
		right: 0;
		bottom: 0;
		overflow: visible;
		> .sweet-buttons {
			display: flex;
			align-items: center;
			justify-content: space-between;
			button {
				margin-left: 0.5rem;
			}
			.metaslider-theme-title {
				font-size: 1.3em;
				margin-top: 0.3em;
			}
		}
	}
	#metaslider-ui .sweet-modal .columns {
		display: flex;
		flex-direction: row;
		.theme-list-column {
			width: 75%;
			position: absolute;
			left: 0;
			top: 0;
			bottom: 0;
			right: 0;
			overflow: auto;
		}
		.theme-details-column {
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			width: 25%;
			background: #f3f3f3;
			border-left: 1px solid #dddddd;
			position: absolute;
			bottom: 0;
			top: 0;
			right: 0;
			height: 100%;
			text-align: left;
			padding: 0 1rem 1rem;
			color: #666;
			[dir='rtl'] & {
				right: auto;
    			left: 0;
			}
			.metaslider-theme-title {
				background-color: #e8e8e8;
				color: #4a4a4a;
				font-size: 1.5em;
				font-weight: 500;
				margin: -1.5rem -1rem 1.5rem;
				padding: 0.5rem 1rem 0.4rem;
			}
			h2, h3 {
				margin: 0;
				margin-top: 1.5rem;
				margin-bottom: .6em;
				color: #666;
				padding: 0;
				font-weight: 600;
				text-transform: uppercase;
				font-size: 1em;
			}
			h2:first-of-type {
				margin-top: 0;
			}
			h3 {
				font-size: 0.9em;
				text-transform: none;
			}
			p {
				line-height: 1.4;
				font-size: 0.9em;
			}
			.ms-theme-description {
				margin-bottom: 2rem;
			}
			ul.ms-theme-tags {
				margin: 0;
				li {
					border-radius: 0.2em;
					display: inline-block;
					margin-right: 0.4em;
					line-height: 1;
					white-space: nowrap;
					font-size: 13px;
					line-height: 1;
					margin-right: 0.4em;
					white-space: nowrap;
					background: lightgray;
					padding: 5px;
					color: #555;
					// border-bottom: 1px solid #b4b6b7;
					// &:hover {
					// 	border-bottom: 1px solid #747b7d;
					// }
				}
			}
		}
	}
	#metaslider-ui .free-themes-not-found {
		max-width: 455px;
		h1 {
			color: $brand;
		}
	}
	#metaslider-ui .ms-image-selector {
		display: flex;
		flex-wrap: wrap;
		margin: 0;
		padding: 0.5rem;
		li {
			background: #fafafa;
			cursor: pointer;
			margin: 0;
			padding: 2px;
			width: 33.3%;
			@include from(1850px) {
				width: 25%;
			}
			@include until(1100px) {
				width: 50%;
			}
			@include until(900px) {
				width: 100%;
			}
			img {
				max-width: 100%;
				display: block;
				width: 100%;
			}
			span {
				border: 4px solid #fafafa;
				height: 100%;
				display: block;
				padding: 2px;
				position: relative;
			}
			@include custom-theme-box();
			&:hover span {
				border-color: #ccc;
			}
			&.selected span {
				border-color: $blue-dark;
			}
		}
	}
	#metaslider-ui .ms-image-selector li.ms-theme-more {
		cursor: default;
		span {
			font-size: 1.5em;
			text-transform: uppercase;
			line-height: 1.3;
			background: #efefef;
			border-color: #FFFFFF !important;
			height: 100%;
			> div {
				padding: 2rem;
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: space-around;
				height: 100%;
				border: 4px solid #eaeaea;
			}
			small {
				font-size: 15px;
				text-transform: initial;
			}
		}
	}

	// Styles for the smaller box
	#metaslider-ui .theme-select-module {
		min-height: 70px;
		.button-info {
			margin-top: 0;
		}
	}
	#metaslider-ui .metaslider-theme-viewer {
		z-index: 3;
		position: relative;
		&.ms-modal-open {
			z-index: 999999;
		}
	}
	#metaslider-ui .theme-select-module .hndle {
		padding-bottom: 0;
	}
	#metaslider-ui .theme-select-module .hndle span {
		color: $brand;
	}
	#metaslider-ui .theme-select-module .slider-not-supported-warning {
		background-color: #f9edc9;
		border: 1px solid #f2a561;
		margin-bottom: 1em;
		padding: 10px 15px;
		svg {
			color: $red !important;
		}
	}
	#metaslider-ui .theme-select-module .sweet-buttons .slider-not-supported-warning {
		margin-bottom: 0;
	}
	#metaslider-ui .theme-select-module .change-theme-img-button {
		img {
			display: block;
			max-width: 100%;
			width: 100%;
		}
	}
	#metaslider-ui .ms-current-theme {
		@include custom-theme-box();

		.custom-theme-single {
			height: 100%;
		}
	}
	#metaslider-ui .ms-current-theme .custom-theme-single .custom-subtitle {
		font-size: 12px;
		font-weight: 300;
		text-transform: uppercase;
		color: #fff;
		margin-bottom: 0.1em;
	}
	#metaslider-ui .custom-theme-single {
		width: 100%;
		height: 100%;
		line-height: normal;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		font-size: 1.3rem;
		font-weight: bold;
		background-color: #2271b1;
		color: white;
		padding: 1rem;
		box-sizing: border-box;
	}
	.regular-themes {
		.a-theme {
			//min-height: 216px;
		}
		.unlock-pro-theme-ad {
			span {
				position: relative
			}
			.custom-theme-single {
				position: absolute;
				z-index: 2;
			}
			img {
				//position: absolute;
				z-index: 1;
				width: calc( 100% - 4px ) !important;
				height: auto !important;
			}
			.upgrade-pro-theme-ad {
				width: calc( 100% - 4px ) !important;
				height: calc( 100% - 4px ) !important;

				@media (max-width: 1199px) and (min-width: 1100px) {
					h3 {
						display: none;
					}
				}
			}
		}
		.unlock-pro-custom-themes-ad {
			.upgrade-pro-theme-ad {
				@media (max-width: 1199px) and (min-width: 1100px) {
					h3 {
						display: none;
					}
				}
			}
		}
	}
	#metaslider-ui .sweet-modal-tabs li.sweet-modal-tab {
		display: none !important;
	}
	@include until(700px) {
		#metaslider-ui .sweet-modal {
			.sweet-content {
				display: block;
			}
			.columns {
				flex-direction: column;
				& > div {
					position: static!important;
					width: 100% !important;
				}
			}
		}
	}
	// Fade the custom theme backgrounds for variety
	// $step:1;
	// $color: $brand;
	// @while $step <=5  {
	// 	#metaslider-ui .custom-themes li:nth-child(10n+#{$step}) > div {
	// 		background: linear-gradient($color, darken($color, (5%)));
	// 	}
	// 	$color: darken($color, (5%));
	// 	$step: $step + 1;
	// }
	// $step:6;
	// @while $step <=10  {
	// 	#metaslider-ui .custom-themes li:nth-child(10n+#{$step}) > div {
	// 		background: linear-gradient($color, lighten($color, (5%)));
	// 	}
	// 	$color: lighten($color, (5%));
	// 	$step: $step + 1;
	// }
</style>
