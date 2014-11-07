/*****************************************************************/
/*                                                               */
/* Description: Search bar function                              */
/* form 5 js                                                     */
/* Author: Yiyang Hao added by Megan Prior-Pfeifer               */
/* Version: 0.0.2                                                */
/*****************************************************************/


    function handleEnter5(inField, e) {  //if you want to add more than one search bar, change this to the corresponding num
        var charCode;
        if(e && e.which){
           charCode = e.which;
        }else if(window.event){
           e = window.event;
           charCode = e.keyCode;
        }

        if(charCode == 13) {
           submitSearch5();      //need to change as the form function name if you add more
           return false;
        }
    }
     
    function submitSearch5() {  //need to change as the form function name if you add more
        if (document.getElementById("search_v5_5").value != "") {   //need to change as the id in form input if you add more
            var searchUrl = "http://search.tomatoink.com/search?keywords=";    //please change the domain name according to your working domain
            window.location = searchUrl + escape(document.getElementById("search_v5_5").value);  //need to change as the id in form input if you add more
        } else {   //please change the domain name according to your working domain
            window.location = searchUrl + escape(document.getElementById("search_v5_5").value = 'ink');  //need to change as the id in form input if you add more
        }
        return false;
    }
    


    function handleEnter6(inField, e) {  //if you want to add more than one search bar, change this to the corresponding num
        var charCode;
        if(e && e.which){
           charCode = e.which;
        }else if(window.event){
           e = window.event;
           charCode = e.keyCode;
        }

        if(charCode == 13) {
           submitSearch6();      //need to change as the form function name if you add more
           return false;
        }
    }
     
    function submitSearch6() {  //need to change as the form function name if you add more
        if (document.getElementById("search_v5_6").value != "") {   //need to change as the id in form input if you add more
            var searchUrl = "http://search.tomatoink.com/search?keywords=";    //please change the domain name according to your working domain
            window.location = searchUrl + escape(document.getElementById("search_v5_6").value);  //need to change as the id in form input if you add more
        } else {   //please change the domain name according to your working domain
            window.location = searchUrl + escape(document.getElementById("search_v5_6").value = 'ink');  //need to change as the id in form input if you add more
        }
        return false;
    }




    function handleEnter7(inField, e) {  //if you want to add more than one search bar, change this to the corresponding num
        var charCode;
        if(e && e.which){
           charCode = e.which;
        }else if(window.event){
           e = window.event;
           charCode = e.keyCode;
        }

        if(charCode == 13) {
           submitSearch7();      //need to change as the form function name if you add more
           return false;
        }
    }
     
    function submitSearch7() {  //need to change as the form function name if you add more
        if (document.getElementById("search_v5_7").value != "") {   //need to change as the id in form input if you add more
            var searchUrl = "http://search.tomatoink.com/search?keywords=";    //please change the domain name according to your working domain
            window.location = searchUrl + escape(document.getElementById("search_v5_7").value);  //need to change as the id in form input if you add more
        } else {   //please change the domain name according to your working domain
            window.location = searchUrl + escape(document.getElementById("search_v5_7").value = 'ink');  //need to change as the id in form input if you add more
        }
        return false;
    }



    function handleEnter8(inField, e) {  //if you want to add more than one search bar, change this to the corresponding num
        var charCode;
        if(e && e.which){
           charCode = e.which;
        }else if(window.event){
           e = window.event;
           charCode = e.keyCode;
        }

        if(charCode == 13) {
           submitSearch8();      //need to change as the form function name if you add more
           return false;
        }
    }
     
    function submitSearch8() {  //need to change as the form function name if you add more
        if (document.getElementById("search_v5_8").value != "") {   //need to change as the id in form input if you add more
            var searchUrl = "http://search.tomatoink.com/search?keywords=";    //please change the domain name according to your working domain
            window.location = searchUrl + escape(document.getElementById("search_v5_8").value);  //need to change as the id in form input if you add more
        } else {   //please change the domain name according to your working domain
            window.location = searchUrl + escape(document.getElementById("search_v5_8").value = 'ink');  //need to change as the id in form input if you add more
        }
        return false;
    }



    function handleEnter9(inField, e) {  //if you want to add more than one search bar, change this to the corresponding num
        var charCode;
        if(e && e.which){
           charCode = e.which;
        }else if(window.event){
           e = window.event;
           charCode = e.keyCode;
        }

        if(charCode == 13) {
           submitSearch9();      //need to change as the form function name if you add more
           return false;
        }
    }
     
    function submitSearch9() {  //need to change as the form function name if you add more
        if (document.getElementById("search_v5_9").value != "") {   //need to change as the id in form input if you add more
            var searchUrl = "http://search.tomatoink.com/search?keywords=";    //please change the domain name according to your working domain
            window.location = searchUrl + escape(document.getElementById("search_v5_9").value);  //need to change as the id in form input if you add more
        } else {   //please change the domain name according to your working domain
            window.location = searchUrl + escape(document.getElementById("search_v5_9").value = 'ink');  //need to change as the id in form input if you add more
        }
        return false;
    }