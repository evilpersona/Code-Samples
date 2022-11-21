@ajax_request
def formsubmitforsalesforce(request):
    dict_return = {'Error':'Call Failed for Unknown Reason'}

    if request.method == 'POST':
        dict_return = {'Error': 'Post Failed'}
        
        email = request.POST.get('email')
        captcha_enabled = request.POST.get('recaptchaenabled',False)
        recaptcha_response = request.POST.get('grecaptcharesponse',None)

        subscribers = EmailSubscribe.objects.filter(email=email)
        dict_return = {'Error' : 'Email Already Exists'}
        
        if subscribers.count() < 1:
            dict_return = {'Error': 'Captcha not Enabled'}
            if captcha_enabled:
            #is response is not available
                dict_return = {'Error': 'Captcha not Valid'}
                if recaptcha_response is not None:
                    #return invalid form

                    data = {
                        'secret': settings.GOOGLE_RECAPTCHA_SECRET_KEY,
                        'response': recaptcha_response
                    }

                    r = requests.post('https://www.google.com/recaptcha/api/siteverify', data=data)
                    result = r.json()
                    dict_return = {'Error': 'Captcha Failed'}
                    if result['success']:
                        

                        data2 = {
                            "grant_type": "client_credentials",
                            "client_id": "14pwvkqkhd7aevd05jt7304n",
                            "client_secret": "R7dhjKluLNJ6gxKDKYaYj6oE",
                        }

                        data2js = json.dumps(data2)

                        r2 = requests.post("https://mc95rkg195r6s21kx6zp0zx3g3h0.auth.marketingcloudapis.com/v2/token", data=data2js, headers = {"Content-Type": "application/json"})
                        
                        dict_return = {'Error': r2.json()}
                        #dict_return['JSON DATA'] = data2js
                        #dict_return['Location'] ='https://mc95rkg195r6s21kx6zp0zx3g3h0.auth.marketingcloudapis.com/v2/token'
                        
                        #mylog = MasterLog(entry_type="Log", message=r2.json(), location="Email Subscribe Form")
                        #mylog.save()
                        if r2.status_code == 200:
                            rs = r2.json()
                            
                            #mylog = MasterLog(entry_type="Log", message=rs, location="Email Subscribe Form")
                            #mylog.save()

                            auth_token = rs["access_token"]
                            bearer_token = "Bearer "

                            auth2 = bearer_token + auth_token


                     

                            #mylog = MasterLog(entry_type="Log", message=fuck2, location="User Info")
                            #mylog.save()
                            

                            data_dict = {
                                    "contactKey": email,
                                        "attributeSets": [{
                                            "name": "Email Addresses",
                                                    "items": [{
                                                        "values": [{
                                                            "name": "Email Address",
                                                            "value": email
                                                        },
                                                        {
                                                            "name": "HTML Enabled",
                                                            "value": "true"
                                                        }]
                                                    }]
                                                }]
                                            }

                            data = json.dumps(data_dict)

                            r = requests.post('https://mc95rkg195r6s21kx6zp0zx3g3h0.rest.marketingcloudapis.com/contacts/v1/contacts', data=data, headers = {"Content-Type": "application/json", "authorization": auth2})
                            
                            dict_return = {'Error': r}
                            #dict_return['Response'] = r
                            #dict_return['Header'] = auth2
                            #dict_return['JSON DATA'] = data
                            #dict_return['Location'] ='https://mc95rkg195r6s21kx6zp0zx3g3h0.rest.marketingcloudapis.com/contacts/v1/contacts/'
                            #mylog = MasterLog(entry_type="Log", message=r.json(), location="Email Subscribe Form")
                            #mylog.save()
                            if r.status_code == 200:
                                result = r.json()
                                #mylog = MasterLog(entry_type="Log", message=result, location="Email Subscribe Form")
                                #mylog.save()
                                new = EmailSubscribe(email=email)
                                new.save()
                                success_email = "techteam@loungelizard.com"
                                success_title = "New Email Subscriber"
                                template_success_email = 'bluefoundry/emails/emailsubscribe.html'
                                send_email(
                                    success_email, 
                                    success_title, 
                                    template_success_email,
                                    data=email
                                    )

                                dict_return = {'Success': 'Email Saved To Salesforce'}
                            else:
                                dict_return = {'Error': "{}{}".format(r.status_code, r.content)}


            
            
            
            
            
        
    else:
        dict_return = {'Error': 'Not A Post Request'}

    return dict_return


@ajax_request
def leadmailboxform(request):
    dict_return = {'Error':'Call Failed for Unknown Reason'}

    if request.method == 'POST':
        dict_return = {'Error': 'Post Failed'}
        
        email = request.POST.get('email')
        first = request.POST.get('first_name')
        last = request.POST.get('last_name')
        phone = request.POST.get('phone_number')
        loan = request.POST.get('loan')
        message = request.POST.get('message')
        captcha_enabled = request.POST.get('recaptchaenabled',False)
        recaptcha_response = request.POST.get('grecaptcharesponse',None)

        
        
        dict_return = {'Error': 'Captcha not Enabled'}
        if captcha_enabled:
            #is response is not available
                dict_return = {'Error': 'Captcha not Valid'}
                if recaptcha_response is not None:
                    #return invalid form

                    data = {
                        'secret': settings.GOOGLE_RECAPTCHA_SECRET_KEY,
                        'response': recaptcha_response
                    }

                    r = requests.post('https://www.google.com/recaptcha/api/siteverify', data=data)
                    result = r.json()
                    dict_return = {'Error': 'Captcha Failed'}
                    if result['success']:
                        

                        data2 = {
                            "FirstName": first,
                            "LastName": last,
                            "Email": email,
                            "HomePhone": phone,
                            "LoanType": loan,
                            "Notes": message,
                        }

                        data2js = json.dumps(data2)

                        r2 = requests.post("https://api.leadmailbox.com/v2/leads/add/bfb01/emailusform", data=data2js, headers = {"Content-Type": "application/json"})
                        
                        dict_return = {'Error': r2.json()}
                        #dict_return['JSON DATA'] = data2js
                        #dict_return['Location'] ='https://mc95rkg195r6s21kx6zp0zx3g3h0.auth.marketingcloudapis.com/v2/token'
                        
                        #mylog = MasterLog(entry_type="Log", message=r2.json(), location="Email Subscribe Form")
                        #mylog.save()
                        if r2.status_code == 200:
                            
                            
                            #mylog = MasterLog(entry_type="Log", message=rs, location="Email Subscribe Form")
                            #mylog.save()

                            
                            success_email = "info@bluefoundrybank.com"
                            success_title = "New Loan Form"
                            template_success_email = 'bluefoundry/emails/emailsubscribe.html'
                            send_email(
                                    success_email, 
                                    success_title, 
                                    template_success_email,
                                    data=email
                                    )

                            dict_return = {'Success': 'Lead Saved'}
                        else:
                            dict_return = {'Error': "{}{}".format(r2.status_code, r2.content)}
                                    
    else:
        dict_return = {'Error': 'Not A Post Request'}

    return dict_return

@ajax_request
def preapprovalform(request):
    dict_return = {'Error':'Call Failed for Unknown Reason'}

    if request.method == 'POST':
        dict_return = {'Error': 'Post Failed'}
        
        email = request.POST.get('email')
        first = request.POST.get('first_name')
        last = request.POST.get('last_name')
        phone = request.POST.get('phone_number')
        loan = request.POST.get('loan')
        message = request.POST.get('message')
        captcha_enabled = request.POST.get('recaptchaenabled',False)
        recaptcha_response = request.POST.get('grecaptcharesponse',None)

        
        
        dict_return = {'Error': 'Captcha not Enabled'}
        if captcha_enabled:
            #is response is not available
                dict_return = {'Error': 'Captcha not Valid'}
                if recaptcha_response is not None:
                    #return invalid form

                    data = {
                        'secret': settings.GOOGLE_RECAPTCHA_SECRET_KEY,
                        'response': recaptcha_response
                    }

                    r = requests.post('https://www.google.com/recaptcha/api/siteverify', data=data)
                    result = r.json()
                    dict_return = {'Error': 'Captcha Failed'}
                    if result['success']:
                        
                            
                            #mylog = MasterLog(entry_type="Log", message=rs, location="Email Subscribe Form")
                            #mylog.save()

                            
                            success_email = "mortgage@bluefoundrybank.com"
                            success_title = "New Pre Approval Request"
                            template_success_email = 'bluefoundry/emails/preapproval.html'
                            send_email(
                                    success_email, 
                                    success_title, 
                                    template_success_email,
                                    data=request.POST
                                    )

                            dict_return = {'Success': 'Lead Saved'}
        
                                    
    else:
        dict_return = {'Error': 'Not A Post Request'}

    return dict_return
