/*
define([
    'jquery',
    'mage/translate',
    'uiComponent',
    'ko',
    'mage/url'
    ], function (
        $,
        $t,
        Component,
        ko,
        urlBuilder
    ) {
    'use strict';
     return Component.extend({
         initialize: function(){
            this._super();
            this.serviceUrl = this.actionUrl;
         },
        deleteCard: function(elemt,event){
            let curElm = $(event.currentTarget), cardId = curElm.data('card');
            
            $.post(this.serviceUrl,{id: cardId},function(resp){
               
            });
        }
      });
    });
    */