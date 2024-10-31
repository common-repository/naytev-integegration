jQuery(document).ready(function() {

  jQuery('#wp_naytev_fb_social_box ul').after(naytevFormOf('facebook'));
  jQuery('#wp_naytev_fb_social_box li').first().addClass('activebox');
  jQuery('#wp_naytev_twt_social_box ul').after(naytevFormOf('twitter'));
  jQuery('#wp_naytev_twt_social_box li').first().addClass('activebox');

    naytevHandleMetaFields();
    naytevAccordClick();
    watchAndDemoTemplate();
    //var id = jQuery(index).attr("id");
    //console.log(this);
    naytevAccordShrink('repeater-wp_naytev_variant_meta-twt');
    naytevAccordShrink('repeater-wp_naytev_variant_meta-fb');
    var twFirstID = jQuery('#repeater-wp_naytev_variant_meta-twt li:first').attr('id');
    var fbFirstID = jQuery('#repeater-wp_naytev_variant_meta-fb li:first').attr('id');
    naytevAccordExpand(twFirstID);
    naytevAccordExpand(fbFirstID);


    naytevArchiveClick();
});

function naytevHandleMetaFields(){
  jQuery("li.wp_naytev_variant_meta").each(function(index){

    jQuery(this).children('input').each(function(index, obj){
      //console.log(obj);
        if (jQuery(obj).val() == jQuery(obj).attr('placeholder')){
          jQuery(obj).val('');
        }
        var field = jQuery(obj).attr('wpn_subfield');
        if (typeof field !== typeof undefined && field !== false && (!jQuery(obj).hasClass('naytev_img_upload_field')) && jQuery(obj).is(":visible")){
          charLimit(obj, field);
        }
      });
      var v_id = jQuery(this).find("input.variant_id");
//      console.log(v_id);
        sv_id = v_id;
//      sv_id = v_id.val();
//      console.log(sv_id.val().length);
      if (1 < sv_id.val().length){
        jQuery(this).children('input').attr('readonly', true);
        jQuery(this).children('input').remove('.fieldcounter');
      }
  });
//  console.log(firstID);

}

function naytevGetVariantField(obj){
  var v_id = jQuery(obj).find("input.variant_id");
  return v_id;
}

function naytevCheckIfSent(obj){
  var v_id = jQuery(obj).children("input.variant_id");
  //console.log(v_id.val());
//      console.log(v_id);
    sv_id = v_id.val();
//      sv_id = v_id.val();
//      console.log(sv_id.length);
    if (1 > sv_id.length){
        return false;
    } else {
      return true;
    }
}

function fbTemplate(obj){
  if (jQuery(obj).hasClass('fb_title')){
    var theVal = jQuery(obj).val();
    //console.log(theVal);
    jQuery('.fb.naytev_template .js-facebookTitle').html(theVal);

  }
  if (jQuery(obj).hasClass('fb_caption')){
    var theVal = jQuery(obj).val();
    //console.log(theVal);
    jQuery('.fb.naytev_template .js-facebookCaption').html(theVal);

  }
  if (jQuery(obj).hasClass('fb_description')){
    var theVal = jQuery(obj).val();
    //console.log(theVal);
    jQuery('.fb.naytev_template .js-facebookDescription').html(theVal);

  }
  if (jQuery(obj).hasClass('fb_image__uploadfield')){
    var theVal = jQuery(obj).val();
    //console.log(theVal);
    jQuery('.fb.naytev_template .js-facebookPostImage').attr('src',theVal);

  }
}

function twTemplate(obj){
  var theVal = jQuery(obj).val();
  //console.log(theVal);
  jQuery('.tw.naytev_template .js-tweetText').html(theVal);
}

function getNaytevInputsFromH3(obj){
  var parent = jQuery(obj).parent('li');
  //console.log(parent);
  var inputs = jQuery(parent).children('input');
  return inputs;
}

function watchAndDemoTemplate(){
  jQuery('#wp_naytev_fb_social_box').on("keyup", "input", function(obj){
    //console.log(obj);
    fbTemplate(this);
  });

  jQuery('#wp_naytev_fb_social_box').on("click", "h3", function(obj){
    var inputs = getNaytevInputsFromH3(this);
    inputs.each(function(){
        //console.log(this)
        fbTemplate(this);
    });

  });

  jQuery('#wp_naytev_fb_social_box .activebox .fb_image__uploadfield').bind("propertychange keyup input paste change", function(obj){
    var theVal = jQuery(this).val();
    jQuery('.fb.naytev_template .js-facebookPostImage').attr('src',theVal);
  });
  setInterval(function() {
    if ((typeof jQuery('#wp_naytev_fb_social_box .activebox .fb_image__uploadfield') != "undefined") && (typeof jQuery('#wp_naytev_fb_social_box .activebox .fb_image__uploadfield').val() != "undefined")){
      var img = jQuery('#wp_naytev_fb_social_box .activebox .fb_image__uploadfield').val();
      if (2 < img.length){
        jQuery('.fb.naytev_template .js-facebookPostImage').attr('src',img);
      }
    }
  }, 1000);

  jQuery('#wp_naytev_twt_social_box').on("keyup", "input", function(obj){
    twTemplate(this);
  });

  jQuery('#wp_naytev_twt_social_box').on("click", "h3", function(obj){
    var parent = jQuery(this).parent('li');
    var input = getNaytevInputsFromH3(this);
    //console.log(input);
    twTemplate(input);
  });

}

function charLimit(obj, wpn_subfield){
  var characters = 0;
  var dcharacters = 100;
  if (('fb_title' == wpn_subfield) || ('fb_caption' == wpn_subfield)){
    dcharacters = 50;
  } else if ('twt_text' == wpn_subfield) {
    dcharacters = 125;
  }
  characters = dcharacters - jQuery(obj).val().length;
  //console.log(characters);
  if ("true" != jQuery(obj).attr('wpn_active')){
    jQuery(obj).before("<span class='fieldcounter char_count_"+wpn_subfield+"'>You have  <strong>"+ characters+"</strong> characters remaining</span><br />");
    jQuery(obj).attr('wpn_active', "true");
  }
  jQuery(obj).keyup(function(){
    if(jQuery(this).val().length > dcharacters){
        jQuery(this).val(jQuery(this).val().substr(0, dcharacters));
    }
    var remaining = dcharacters -  jQuery(this).val().length;

    var theParentId = jQuery(obj).parent('li').attr('id');
    //console.log(theParentId);
    jQuery("#"+theParentId + " .char_count_"+wpn_subfield).html("You have  <strong>"+ remaining+"</strong> characters remaining");

  });

}

function naytevFormOf(a){
  var twt = jQuery('#wp_naytev_template_box .tw');
  //console.log(twt);
  var fb = jQuery('#wp_naytev_template_box .fb');;

  if ('twitter' == a){
    return twt;
  } else if ('facebook' == a){
    return fb;
  } else {
    return false;
  }

}

function naytevAccordClick(){
  jQuery('#post-body').on("click", ".naytev h3.repeat-meta", function(obj){
    //console.log(jQuery(this).attr('class'));
    var element = jQuery(this).parent('.repeat-element');
    var id = jQuery(element).attr("id");
    var parentE = jQuery(this).parents('.repeater-container');
    var parentid = parentE.attr('id');
    if (jQuery(this).hasClass('inactive')){
      naytevAccordShrink(parentid);
      naytevAccordExpand(id);
      //console.log(id);
    } else {
      naytevAccordShrink(parentid);
      //console.log(parentid);
    }
  })
}

function naytevAccordShrink(id){

  jQuery("#"+id+" li.repeat-element > *").hide();
  jQuery("#"+id+" .armArchive").remove();
  jQuery("#"+id+" li.repeat-element").removeClass('activebox');
  jQuery("#"+id+" li.repeat-element > h3").show();
  //var header = jQuery("#"+id+" li.repeat-element > h3").html();
  jQuery("#"+id+" li.repeat-element").each(function(index, element){
      var title = jQuery(this).children('input').first().val();
      var titleSubed = title.substring(0,33);
      var titleDone = titleSubed+'...';
      var thisID = jQuery(this).attr('id');
      //console.log(thisID);
      jQuery("#"+thisID+" .heading-content").html(titleDone);
  });

  jQuery("#"+id+" li.repeat-element > h3").addClass('inactive');
  //var header = jQuery("#"+id+" li.repeat-element > h3 > .expandState").html();
  jQuery("#"+id+" li.repeat-element > h3 > .expandState").html("+ ");
  naytevFetchStats();
}

function naytevAccordExpand(id){
  jQuery("#"+id+" > *").show();
  jQuery("#"+id+" > h3 > .expandState").html("- ");
  jQuery("#"+id+" > h3").removeClass('inactive');
  jQuery("#"+id).addClass('activebox');
  jQuery("#"+id+" > .repeat-element-remover").after(naytevArchiveButton());
}

function naytevArchiveButton(){
  var button = '<button class="btn btn-sm btn-danger armArchive"><i class="fa fa-power-off"></i> Archive</button>';
  return button;
}

function naytevFetchStats(){

  jQuery.each(jQuery( '#post-body' ).find('li.wp_naytev_variant_meta'), function(i,v) {
//    console.log(v);
    var theVariant = this;
    var statsString = '';
    var status = true;
    if(naytevCheckIfSent(this)){
      var v_id = naytevGetVariantField(this);
      var sv_id = v_id.val();
//      console.log(sv_id);
      jQuery.post(ajaxurl, {
        action: 'get_naytev_stats_via_ajax',
        naytev_variant_id: sv_id
      },
      function(response){
//        console.log('Response: ');
//        console.log(response);
        var theVariantValue = jQuery(response).find('naytev_stats').attr('id');
        if (('' == jQuery(response).find("response_data").text()) || ('null' == jQuery(response).find("response_data").text())){
//          console.log('null response for ' + sv_id);
          statsString = "No shares yet!";
        } else {
          var totalInteractions = jQuery(response).find("totalInteractions");
          var clicksPerShare = jQuery(response).find("clicksPerShare");
          var twitterRetweets = jQuery(response).find("retweets");
          var clicks = jQuery(response).find("clicks");
          var favorites = jQuery(response).find("favorites");
          var statusObj = jQuery(response).find("statusOf");
          status = statusObj.text();
//          console.log(status);
          totalInteractions = totalInteractions.text();
          if (totalInteractions == 0){
            statsString = 'No shares yet!';
          } else {
            statsString = "Interactions: " + totalInteractions;
          }
        }
        if ((status == 'archived') || (status == 'inactive')){
          status = false;
//          console.log('Archived');
          jQuery(theVariant).hide();
        } else {
//          console.log(theVariantValue);
          var thisVariantByID = jQuery('input[value="'+theVariantValue+'"]').closest('li');
//          console.log(thisVariantByID);
          var thisVariantID = jQuery(thisVariantByID).attr('id');
//          console.log('ID: '+thisVariantID);
          var thisVariantSelect = jQuery('#'+thisVariantID);
          var variantTitleBar = thisVariantSelect.find('h3');
          //console.log(thisVariantSelect);
          if (variantTitleBar.children('.naytev-stats').length){
            variantTitleBar.children('.naytev-stats').html(statsString);
          } else {
            variantTitleBar.append('<span class="naytev-stats">'+statsString+'</span>');
          }
        }

      });

    }
  });
}

function naytevFetchVariantStats(sv_id){
  var r = '';
  jQuery.post(ajaxurl, {
    action: 'get_naytev_stats_via_ajax',
    naytev_variant_id: sv_id
  },
  function(response){
    r = response;
    return response;
  });
  return r;
}

function naytevVariantStatus(){
  jQuery( 'li.wp_naytev_variant_meta' ).each(function() {
//    console.log(naytevCheckIfSent(this));
    var theVariant = this;
    var status = true;
    if(naytevCheckIfSent(this)){
      var v_id = naytevGetVariantField(this);
//      console.log(v_id);
      var sv_id = v_id.val();
//      console.log(sv_id);
      //console.log(sv_id);
      jQuery.post(ajaxurl, {
        action: 'naytev_variant_status_is',
        naytev_variant_id: sv_id
      },
      function(response){
//        console.log(response);
        var theVariantValue = jQuery(response).find('naytev_status').attr('id');
        if ('null' == jQuery(response).find("response_data").text()){
          //console.log('null response for ' + sv_id);
//          console.log("No response to status request for " + sv_id);
        } else {
          var statusResponse = jQuery(response).find("response_data").text();
          if (statusResponse == 'archived' || statusResponse == 'inactive'){
            jQuery(theVariant).hide();
          }
        }

      });

    }
  });
}

function naytevArchiveClick(){
  jQuery("#post-body").on('click', ".armArchive", function(obj){
      console.log(obj);
      var objParent = jQuery(obj.currentTarget).parent('li.wp_naytev_variant_meta.activebox');
      naytevArchiveVariant(objParent);
      return false;
  });
}

function naytevArchiveVariant(obj){
  console.log(obj);
  if(naytevCheckIfSent(obj)){
    var v_id = naytevGetVariantField(obj);
//      console.log(v_id);
    var sv_id = v_id.val();
//      console.log(sv_id);
    //console.log(sv_id);
    jQuery.post(ajaxurl, {
      action: 'naytev_variant_status_is',
      naytev_variant_id: sv_id,
      naytev_status_set: 'archived'
    },
    function(response){
//        console.log(response);
      var theVariantValue = jQuery(response).find('naytev_status').attr('id');
      if ('null' == jQuery(response).find("response_data").text()){
        //console.log('null response for ' + sv_id);
//          console.log("No response to status request for " + sv_id);
      } else {
        var statusResponse = jQuery(response).find("response_data").text();
        if (statusResponse == 'archived' || statusResponse == 'inactive'){
          jQuery(obj).hide();
        }
      }

    });

  }
}


function naytevVariantSetArchived(sv_id){

}

function naytevStateArchiveVariant(){

}
