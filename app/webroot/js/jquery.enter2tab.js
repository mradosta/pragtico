/*
 *
 * Copyright (c) 2007 Betha Sidik (bethasidik at gmail dot com)
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * This plugin developed based on jquery-numeric.js developed by Sam Collett (http://www.texotela.co.uk)
 */

/*
 * Change the behaviour of enter key pressed in web based to be tab key
 * So if this plugin used, a enter key will be a tab key
 * User must explicitly give a tabindex in element such as text or select
 * this version will assumed one form in a page
 * applied to element text and select
 *
 */
jQuery.fn.enter2tab = function()
{
   this.keypress(
   function(e)
   {

      // get key pressed (charCode from Mozilla/Firefox and Opera / keyCode in IE)
      var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;

      // get tabindex from which element keypressed
      var nTabIndex=this.tabIndex+1;

      // get element type (text or select)
      var myNode=this.nodeName.toLowerCase();

      // allow enter/return key (only when in an input box or select)
      if(key == 13 && ((myNode == "input") || (myNode == "select")))
      {
         $("select[@tabIndex='"+nTabIndex+"'],input[@tabIndex='"+nTabIndex+"']").get(0).focus();
          return false;
      }
      else if(key == 13)
      {
          return false;
      }
   }
   )
   return this;
}