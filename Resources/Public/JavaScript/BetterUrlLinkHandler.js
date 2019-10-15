/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Module: TYPO3/CMS/Recordlist/UrlLinkHandler
 * URL link interaction
 */
define(['jquery', 'TYPO3/CMS/Recordlist/LinkBrowser'], function ($, LinkBrowser) {
  'use strict';

  /**
   *
   * @type {{}}
   * @exports TYPO3/CMS/Recordlist/UrlLinkHandler
   */
  var UrlLinkHandler = {};

  /**
   *
   * @param {Event} event
   */
  UrlLinkHandler.link = function (event) {
    event.preventDefault();

    var value = $(this).find('[name="lurl"]').val();
    if (value === '') {
      return;
    }

    $.ajax({
      url: TYPO3.settings.ajaxUrls['uri2link_check'],
      data: {
        uri: value
      }
    }).done(function (data) {

      if (data.transformed) {
        LinkBrowser.finalizeFunction(data.transformed);
      } else {
        LinkBrowser.finalizeFunction(value);
      }
    }).fail(function (data) {
      LinkBrowser.finalizeFunction(value);
    });
  };

  $(function () {
    $('#lurlform').on('submit', UrlLinkHandler.link);
  });

  return UrlLinkHandler;
});


