function DaysBetween(StartDate, EndDate) {
    // The number of milliseconds in all UTC days (no DST)
    const oneDay = 1000 * 60 * 60 * 24;
  
    // A day in UTC always lasts 24 hours (unlike in other time formats)
    const start = Date.UTC(EndDate.getFullYear(), EndDate.getMonth(), EndDate.getDate());
    const end = Date.UTC(StartDate.getFullYear(), StartDate.getMonth(), StartDate.getDate());
  
    // so it's safe to divide by 24 hours
    return (start - end) / oneDay;
  }


  function wowNow(startDate,endDate,attractions){
    
    var endDate = endDate;
    var startDate = startDate;
    var attractions = attractions;
    jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: wow_admin_URL.admin_URL,
        data: {action:'wowNow','startDate':startDate,'endDate':endDate,'attractions':attractions},
        success: function (response) {
            if(response) {
                console.log(response.avail);
                console.log(response.ids);
                jQuery('#searchButton').text('Search Availability');
                
                jQuery('#availSigns').empty();
                //jQuery('#notavailSigns').empty();
                response.ids.forEach(element => {
                    jQuery('.'+element).addClass('save');
                });
                jQuery('.info-window').each(function(){
                    if(!jQuery(this).hasClass('save')){
                        jQuery(this).remove();
                    }
                })
                jQuery('.acf-map-select').html(response.map);
                jQuery('#availSigns').append(response.avail);
                
                jQuery('.acf-map-select').each(function(){
                    var map = initMap( jQuery(this) );
                    
                });
                jQuery('.bulletin-wrap').addClass('flexDisp');
                //jQuery('#notavailSigns').append(response.notavail);
                //jQuery('.formSection').show();
                
                jQuery('.startDate').empty();
                jQuery('.endDate').empty();
                jQuery('.signs').empty();
                
                signs = [];
                signNames = [];
                
                var calcStart = new Date(startDate);
                var calcEnd = new Date(endDate);
                jQuery('.startDate').append(calcStart.toLocaleString('en-us',{ year: 'numeric', month: '2-digit', day: '2-digit' }));
                jQuery('.endDate').append(calcEnd.toLocaleString('en-us',{ year: 'numeric', month: '2-digit', day: '2-digit' }));
                var diff = DaysBetween(calcStart,calcEnd);
                if(diff == 0){
                    diff = 1;
                }
                

                jQuery('.numDays').val(diff);
                jQuery('.startBook').val(startDate);
                jQuery('.endBook').val(endDate);
                //jQuery('html, body').animate({ scrollTop:  jQuery('.bulletins-section').offset().top - 50 }, 'slow');
                
                
            } else {
               
            }
            
            
        },
        error: function (argument,jqXHR, textStatus) {
            alert( "Request failed: " + textStatus );
            console.log(jqXHR);
            console.log(argument);
            alert('error: '+ argument );
        }
    });
}

function getSavedOrders(){
    

    jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: wow_admin_URL.admin_URL,
        data: {action:'getSavedOrders'},
        success: function (response) {
            if(response) {
                
                console.log(response);
                jQuery('#wowNowSaved').append(response);
                jQuery('.login').hide();
                jQuery('#wowNowSaved').show();


            } else {
               
            }
            
            
        },
        error: function (argument,jqXHR, textStatus) {
            alert( "Request failed: " + textStatus );
            console.log(jqXHR);
            console.log(argument);
            alert('error: '+ argument );
        }
    });
}


function getSelectedSigns(selected){
    
    
    var selected = selected;
    jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: wow_admin_URL.admin_URL,
        data: {action:'getSelectedSigns','signNames': selected},
        success: function (response) {
            if(response) {
                //console.log(response.avail);
                console.log(response.map);
                
                var startDate = jQuery('#startDate').val();
                var endDate = jQuery('#endDate').val();
                //jQuery('#notavailSigns').empty();
                jQuery('.bilSum').html(response.avail);
                jQuery('.mapSum').html(response.map);

                jQuery('.selectedSigns .dates span').html(jQuery('#startDate').val() + ' to ' + jQuery('#endDate').val());
                var calcStart = new Date(startDate);
                var calcEnd = new Date(endDate);
                var diff = DaysBetween(calcStart,calcEnd);
                if(diff == 0){
                    diff = 1;
                }
                jQuery('.selectedSigns .days span').html(diff + ' Days');
                //jQuery('#notavailSigns').append(response.notavail);
                //jQuery('.formSection').show();
                jQuery('.mapSum').each(function(){
                    var map = initMap( jQuery(this) );
                    
                });
                
               
                //jQuery('html, body').animate({ scrollTop:  jQuery('.bulletins-section').offset().top - 50 }, 'slow');
                
                
            } else {
               
            }
            
            
        },
        error: function (argument,jqXHR, textStatus) {
            alert( "Request failed: " + textStatus );
            console.log(jqXHR);
            console.log(argument);
            alert('error: '+ argument );
        }
    });
}

    function getProduct(numDays,numSigns,actSigns){
    
        var endDate = numDays;
        var startDate = numSigns;
        var signs = actSigns;
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: wow_admin_URL.admin_URL,
            data: {action:'getProduct','signs':startDate,'days':endDate},
            success: function (response) {
                if(response) {
					console.log(response.var);
                    console.log(response.price);
                    var formatter = new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'USD',
                      
                        // These options are needed to round to whole numbers if that's what you want.
                        //minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
                        //maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
                      });
                    jQuery('.variation').val(response.var);
                    jQuery('.price2').empty();
                    jQuery('.price2').append(response.price);
                    var pricePer = response.priceNF / signs;
                    
                    
                    jQuery('.pricePer').empty();
                    jQuery('.checked .pricePer').append(formatter.format(pricePer));
                    jQuery('.pricePers').append(formatter.format(pricePer));
                
                    
                    
                } else {
                   
                }
                
                
            },
            error: function (argument,jqXHR, textStatus) {
				alert( "Request failed: " + textStatus );
				console.log(jqXHR);
                console.log(argument);
                alert('error: '+ argument );
            }
        });
    }

    function getSavings(numDays,numSigns,actSigns){
    
        var endDate = numDays;
        var startDate = numSigns;
        var signs = actSigns;

        console.log(endDate);
        var origDays = endDate;
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: wow_admin_URL.admin_URL,
            data: {action:'getSavings','signs':startDate,'days':origDays},
            success: function (response) {
                if(response) {
					console.log(response);
                    //console.log(response.price);
                    var formatter = new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'USD',
                      
                        // These options are needed to round to whole numbers if that's what you want.
                        //minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
                        //maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
                      });
                    
                    
                    
                    
                    var pricePer = response.priceNF / signs;

                    var multipleSigns = response.priceOne * startDate;


                    console.log('Multiple Signs: '+multipleSigns);


                      if(endDate >= 8){
                          endDate = endDate - 8

                        
                          var dayPrice =  response.priceDay * endDate;
                          console.log('8 Day Price: '+ dayPrice);

                            console.log('1 sign for Total Days: '+ response.priceF)
    
                          var day2 = response.priceF - response.priceDay;
    
                          console.log('Multiple Day Savings: '+day2);
    
                          var day3 = startDate * day2;
    
                          console.log('Savings across Signs: '+day3);

                      }else{
                          
                        var dayPrice =  response.priceDay * endDate;
                        console.log('1 Day Price: '+dayPrice);
  
                        var day2 = dayPrice - response.priceOne;
  
                        console.log('Savings for days: '+day2);
  
                        var day3 = startDate * day2;
  
                        console.log('Total Day: '+day3);

                      }

                      //console.log('Days: '+endDate);
                      //console.log('1 Day Price for '+startDate+' signs: ' + response.priceDay);
                      
                      
                      

                    var savings1 = multipleSigns - response.priceNF;

                    console.log('Multiple Sign Savings: '+ savings1);


                    var savings2 = day3;

                   // console.log('Multiple Day Savings: '+savings2);
                    var savings = savings1 + savings2;

                    if (savings < 0){
                        savings = savings * -1
                    }
                    
                    console.log('Savings: '+savings);
                
                    jQuery('.savings').text(formatter.format(savings));
                    jQuery('.avgPrice').text(formatter.format(pricePer));
                
                    
                    
                } else {
                   
                }
                
                
            },
            error: function (argument,jqXHR, textStatus) {
				alert( "Request failed: " + textStatus );
				console.log(jqXHR);
                console.log(argument);
                alert('error: '+ argument );
            }
        });
    }

    function getUser(){
    
        
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: wow_admin_URL.admin_URL,
            data: {action:'getUser'},
            success: function (response) {
                if(response) {
					
                jQuery('.firstName').val(response.first);
                jQuery('.lastName').val(response.last);
                jQuery('.email').val(response.email);   
                jQuery('.userID').val(response.userID);                 
                jQuery('.phoneNumber').val(response.phone); 
                jQuery('.companyName').val(response.company); 
                } else {
                   
                }
                
                
            },
            error: function (argument,jqXHR, textStatus) {
				alert( "Request failed: " + textStatus );
				console.log(jqXHR);
                console.log(argument);
                alert('error: '+ argument );
            }
        });
    }


    function getUserID(){
    
        
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: wow_admin_URL.admin_URL,
            data: {action:'getUser'},
            success: function (response) {
                if(response) {
					
                
                return response.userID;                 
                    
                } else {
                   
                }
                
                
            },
            error: function (argument,jqXHR, textStatus) {
				alert( "Request failed: " + textStatus );
				console.log(jqXHR);
                console.log(argument);
                alert('error: '+ argument );
            }
        });
    }
