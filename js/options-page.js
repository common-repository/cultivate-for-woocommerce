( function ( $ ) {
	$( '[data-behavior="expandHeading"]' ).each( function () {
		var $target = $( this ).find( '+ *:eq(0)' );

		$( this ).on( 'click', function () {
			var active = ! $target.is( ':visible' );
			$target.toggle( active );
			$( this ).toggleClass( 'active', active );
		} );
	} );

	$( '[data-behavior="cultivateDynamicContent"]' ).each( function () {
		var $pendingMessage = $( this ).find( '[data-pending]' ),
			$termsContent = $( this ).find(
				'[data-behavior="termsContainer"]'
			),
			$merchantTermsAcceptedContent = $( this ).find(
				'[data-behavior="merchantTermsAcceptedContainer"]'
			),
			$generalErrorEl = $( this ).find(
				'[data-behavior="generalError"]'
			);

		var apiBaseUrl = $( this ).data( 'apiBaseUrl' ),
			authorizeUrl = $( this ).data( 'authorizeUrl' ),
			siteUrl = $( this ).data( 'siteUrl' ),
			pluginVersion = $( this ).data( 'pluginVersion' );

		var token = window.localStorage.getItem( 'cultivating' );
		if ( ! token ) {
			window.location = authorizeUrl;
			return;
		} else {
			window.localStorage.removeItem( 'cultivating' );
		}

		var loadTermsContent = initTermsContent(),
			loadMerchantTermsAcceptedContent = initMerchantTermsAcceptedContent();

		loadFullUI();

		function loadFullUI() {
			$termsContent.hide();
			$merchantTermsAcceptedContent.hide();
			$pendingMessage.show();

			invokeApi( {
				url: '/shop/status',
				success: function ( status ) {
					if ( ! status.isMerchantTermsAccepted ) {
						loadTermsContent();
					} else {
						loadMerchantTermsAcceptedContent( status );
					}
				},
			} );
		}

		function initTermsContent() {
			var $el = $termsContent,
				$termsHtml = $el.find( '[data-terms-html]' ),
				$form = $el.find( '[data-form]' );

			var siteName = $el.data( 'siteName' ),
				timezone = $el.data( 'timezone' );

			$form.on( 'submit', function () {
				setTermsAccepted( loadFullUI );
				return false;
			} );

			return function () {
				getTermsHtml();
			};

			function getTermsHtml() {
				invokeApi( {
					url: '/terms',
					success: function ( res ) {
						updateTermsUI( res.terms );
					},
				} );
			}

			function updateTermsUI( html ) {
				$pendingMessage.hide();
				$el.show();
				$termsHtml.html( html );
			}

			function setTermsAccepted( cb ) {
				invokeApi( {
					url: '/shop/status',
					type: 'patch',
					data: [
						{
							op: 'replace',
							path: '/isMerchantTermsAccepted',
							value: true,
						},
						{ op: 'replace', path: '/timezone', value: timezone },
						{
							op: 'replace',
							path: '/name',
							value: siteName || 'UNNAMED SITE',
						},
					],
					success: cb,
				} );
			}
		}

		function initMerchantTermsAcceptedContent() {
			var $el = $merchantTermsAcceptedContent,
				$payItForwardContent = $el.find(
					'[data-behavior="payItForwardContainer"]'
				),
				$settingsContent = $el.find(
					'[data-behavior="settingsContainer"]'
				);

			var loadPayItForwardContent = initPayItForwardContent(),
				loadSettingsContent = initSettingsContent();

			return function ( status ) {
				if ( status.isSetupComplete ) {
					loadPayItForwardContent( status );
				}
				loadSettingsContent( status );
			};

			function initPayItForwardContent() {
				var $el = $payItForwardContent,
					$enable = $el.find( '[data-enable]' ),
					$disable = $el.find( '[data-disable]' );

				$enable.on( 'click', function () {
					setPayItForwardStatus(
						'AWAITING_APPROVAL',
						reloadPayItForwardStatus
					);
				} );

				$disable.on( 'click', function () {
					setPayItForwardStatus(
						'INACTIVE',
						reloadPayItForwardStatus
					);
				} );

				return function ( status ) {
					updateUI( status );
				};

				function updateUI( res ) {
					$pendingMessage.hide();
					$merchantTermsAcceptedContent.show();
					$el.show();

					updateUIMessage( res.payItForwardStatus );
					switch ( res.payItForwardStatus ) {
						case 'ACTIVE':
							updateUIEnableEnabled( false );
							updateUIDisableEnabled( true );
							break;
						case 'AWAITING_APPROVAL':
							updateUIEnableEnabled( false );
							updateUIDisableEnabled( false );
							break;
						default:
							updateUIEnableEnabled( true );
							updateUIDisableEnabled( false );
							break;
					}
				}

				function updateUIMessage( status ) {
					$el.find( '[data-status]' ).each( function () {
						$( this ).toggle(
							$( this ).data( 'status' ) === status
						);
					} );
				}

				function updateUIEnableEnabled( bool ) {
					$enable.prop( 'disabled', ! bool );
				}

				function updateUIDisableEnabled( bool ) {
					$disable.prop( 'disabled', ! bool );
				}

				function reloadPayItForwardStatus() {
					invokeApi( {
						url: '/shop/status',
						success: updateUI,
					} );
				}

				function setPayItForwardStatus( status, cb ) {
					invokeApi( {
						url: '/shop/status',
						type: 'patch',
						data: [
							{
								op: 'replace',
								path: '/payItForwardStatus',
								value: status,
							},
						],
						success: cb,
					} );
				}
			}

			function initSettingsContent() {
				var $el = $settingsContent,
					$expandHeading = $el.find(
						'[data-behavior="expandHeading"]'
					),
					$form = $el.find( '[data-form]' ),
					$contactName = $el.find( 'input[name="contactName"]' ),
					$organizationName = $el.find(
						'input[name="organizationName"]'
					),
					$telephone = $el.find( 'input[name="telephone"]' ),
					$email = $el.find( 'input[name="email"]' ),
					$isWomanOwnedOrOperated = $el.find(
						'input[name="isWomanOwnedOrOperated"]'
					),
					$isBipocOwnedOrOperated = $el.find(
						'input[name="isBipocOwnedOrOperated"]'
					),
					$hqState = $el.find( 'select[name="hqState"]' ),
					$shopLogo = $el.find( 'input[name="file"]' ),
					$shopLogoProgressBar = $el.find(
						'#shopLogoProgress > div'
					),
					$shopLogoThumbnail = $el.find( '#shopLogoThumbnail' ),
					$shopLogoError = $el.find( '#shopLogoError' ),
					$submit = $el.find( 'button[type="submit"]' );

				var $fields = $( [
					$contactName[ 0 ],
					$organizationName[ 0 ],
					$telephone[ 0 ],
					$email[ 0 ],
					$isWomanOwnedOrOperated[ 0 ],
					$isBipocOwnedOrOperated[ 0 ],
					$hqState[ 0 ],
					$shopLogo[ 0 ],
				] );

				var loading = false,
					valid = true;

				$shopLogo.fileupload( {
					url: apiBaseUrl + '/shop/settings/site/image',
					paramName: 'file',
					headers: getHeaders(),
					formData: [],
					submit: function ( e, data ) {
						$shopLogoError.empty();
					},
					progressall: function ( e, data ) {
						var progress = parseInt(
							( data.loaded / data.total ) * 100,
							10
						);
						$shopLogoProgressBar.css( 'width', progress + '%' );
					},
					done: function ( e, data ) {
						updateShopLogoThumbnail( data.result.thumbnail );
					},
					error: handleApiError( $shopLogoError, function () {
						updateShopLogoThumbnail( null );
					} ),
					always: function () {
						valid = isValid();
						updateUIEnabled();
					},
				} );

				$fields.on( 'input', function () {
					valid = isValid();
					updateUIEnabled();
				} );

				$form.on( 'submit', function () {
					loading = true;
					updateUIEnabled();
					saveSettings(
						{
							contactName: $contactName.val(),
							organizationName: $organizationName.val(),
							telephone: $telephone.val(),
							email: $email.val(),
							isWomanOwnedOrOperated: $isWomanOwnedOrOperated.is(
								':checked'
							),
							isBipocOwnedOrOperated: $isBipocOwnedOrOperated.is(
								':checked'
							),
							hqState: $hqState.val(),
						},
						function () {
							window.location.reload();
						}
					);
					return false;
				} );

				return function ( status ) {
					loadUI( status );
				};

				function loadUI( status ) {
					getSettings( function ( res ) {
						updateUI( res, status );
					} );
				}

				function updateUI( res, status ) {
					$pendingMessage.hide();
					$merchantTermsAcceptedContent.show();
					$el.show();

					if ( ! status.isSetupComplete ) {
						$expandHeading.click().hide();
					}

					if ( res.contactName ) {
						$contactName.val( res.contactName );
					}
					if ( res.organizationName ) {
						$organizationName.val( res.organizationName );
					}
					if ( res.telephone ) {
						$telephone.val( res.telephone );
					}
					if ( res.email ) {
						$email.val( res.email );
					}

					$isWomanOwnedOrOperated.prop(
						'checked',
						!! res.isWomanOwnedOrOperated
					);
					$isBipocOwnedOrOperated.prop(
						'checked',
						!! res.isBipocOwnedOrOperated
					);

					if ( res.hqState ) {
						$hqState.val( res.hqState );
					}

					if ( res.thumbnail ) {
						updateShopLogoThumbnail( res.thumbnail );
					}

					loading = false;
					valid = isValid();
					updateUIEnabled();
				}

				function updateUIEnabled() {
					$fields.prop( 'disabled', loading );
					$submit.prop( 'disabled', loading || ! valid );
				}

				function updateShopLogoThumbnail( thumbnail ) {
					$shopLogoThumbnail.empty();

					if ( thumbnail ) {
						$shopLogoThumbnail.append(
							$(
								'<img width="100" alt="" src="' +
									thumbnail +
									'">'
							)
						);
					}
				}

				function isValid() {
					var result = true;
					$fields.each( function () {
						if ( ! isFieldValid( this ) ) {
							result = false;
							return false;
						}
					} );
					return result;
				}

				function isFieldValid( field ) {
					return field === $shopLogo[ 0 ]
						? !! $shopLogoThumbnail.find( 'img' ).length
						: !! $( field ).val().trim();
				}

				function getSettings( cb ) {
					invokeApi( {
						url: '/shop/settings',
						success: cb,
					} );
				}

				function saveSettings( settings, cb ) {
					invokeApi( {
						url: '/shop/settings',
						type: 'put',
						data: settings,
						success: cb,
						error: function () {
							loading = false;
							updateUIEnabled();
						},
					} );
				}
			}
		}

		function invokeApi( params ) {
			$generalErrorEl.empty();
			$.ajax( {
				url: apiBaseUrl + params.url,
				type: params.type || 'get',
				headers: getHeaders(),
				contentType: params.data ? 'application/json' : undefined,
				data: params.data ? JSON.stringify( params.data ) : undefined,
				dataType: 'json',
				success: params.success,
				error: handleApiError( $generalErrorEl, params.error ),
				complete: params.complete,
			} );
		}

		function getHeaders() {
			return {
				'x-cultivate-woocommerce-shop': siteUrl,
				'x-cultivate-woocommerce-plugin-version': pluginVersion,
				Authorization: 'Bearer ' + token,
			};
		}

		function handleApiError( $errorEl, cb ) {
			return function ( jqXhr, textStatus, errorThrown ) {
				if ( cb ) {
					cb();
				}
				if ( ! jqXhr.status || jqXhr.status === 401 ) {
					window.location = authorizeUrl;
				} else {
					console.error(
						jqXhr.status +
							', textStatus=' +
							textStatus +
							', errorThrown=' +
							errorThrown
					);
					var res = jqXhr.responseText;
					try {
						var msg = JSON.parse( res ).error.message;
						if ( msg ) {
							res = msg;
						}
					} catch ( err ) {
						console.log( 'unparsable error response: ' + res );
					}
					$errorEl.text( res );
				}
			};
		}
	} );
} )( jQuery );
