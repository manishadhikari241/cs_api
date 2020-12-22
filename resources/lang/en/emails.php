<?php

return [
    // registration
    'register.subject'                             => 'Welcome to Collectionstock!',
    'register.greetings'                           => 'Congratulations!',
    'register.message'                             => 'You successfully joined Collectionstock and from now on you can start using',
    'register.login.1'                             => 'To log in when visiting our site, just click',
    'register.login.2'                             => 'Log in',
    'register.login.3'                             => 'at the top of any page and enter the following information:',
    'register.username'                            => 'Your Username:',
    'register.coupon'                              => 'Please enjoy 10% OFF your first Purchase at Collectionstock.com',
    'register.coupon.code'                         => 'Just enter “:code” at checkout.',
    'register.coupon.note'                         => 'Please note that the discount cannot be used with any other promotion code. It will expire within 30 days.',
    'register.data.protect'                        => '
    <h3>Some info on how we protect your information!</h3>
    <h3>Our Updated Privacy Policy compliant with GDPR</h3>
    <p>Collectionstock is committed to provide the best graphic designs to professionals. To support growth, and help ensure Collectionstock and our customers remain compliant with upcoming changes to the EU data protection law (General Data Protection Regulation, or “GDPR”), we’ve made a number of updates to our Privacy Policy.</p>
    <p>Our updated policy provides more details on:</p>
    <ul>
        <li>the information that we collect;</li>
        <li>how we use this information, why we store, and why we retain it; and</li>
        <li>how you can request that your information is updated, corrected, or deleted.</li>
    </ul>
    <p>
        When you registered yourself as a User on our website, you have already accepted our Privacy Policy. Read our full Privacy Policy <a href="https://collectionstock.com/legal/privacy-policy" target="_blank">here</a>, If you not agree with this Privacy Policy, you can stop using or services and contact us at help@collectionstock.com to remove you as a registered user from our website
    </p>
    <h3>Receiving our newsletters</h3>
    ',
    'register.subscribe.yes'                        => '
    <p>
        When you registered yourself as a User on our website, you have given us consent to receive our newsletter and latest offers by selecting and tick marking the option:
    </p>
    <p style="font-style: italic;">
        Yes, I Would Like To Subscribe To The Collectionstock Newsletter And Latest Offers.
    </p>
    <p>
        Because we already have received your consent, you will have to take no further action to keep receiving our newsletters and latest offers.
    </p>
    <p>
        If you however would like to opt-out and not receive our newsletters anymore, you can simply login into “Your Account” and make the appropriate selection under “Newsletter Status”.
    </p>
    ',
    'register.subscribe.no'                         => '
    <p>
        When you registered yourself as a User on our website, you have NOT given us consent to receive our newsletter and latest offers
    </p>
    <p>
        We will keep it this way and respect your choice.
    </p>
    <p>
        If you however would change your mind and decide to give your consent to receive our newsletters and latest offers, you can always simply login into “Your Account” and make the appropriate selection under “Newsletter Status”.
    </p>
    ',

    // verify
    'verify.subject'                               => 'Collectionstock Verification Code :code',
    'verify.message'                               => 'You have requested to change your email. <br> Please click this link to activate your account:',
    'verify.click'                                 => 'Verify NOW',
    'verify.email_code'                            => 'Your verification code is ":code". Enter this code in the verification form on the Collectionstock website.',
    // 'verify.email_code'                            => 'Your activation code is ":code". Enter this code in the verification form on the Collectionstock website or you can click this <a href=":link">link</a> to activate your account',

    // forget password
    'forget.subject'                               => 'Collectionstock – Reset Your Password',
    'request.email'                                => 'This e-mail is being sent because you requested to reset your password.',
    'password.change'                              => 'You can change your password by clicking the link below which is only valid for 24 hours:',
    'change.click'                                 => 'RESET PASSWORD',
    'privacy.change'                               => 'This process is designed to ensure the privacy and security of your account information. If you did not make this request, you can ignore this message and your password will remain the same.',

    // password updated
    'password.subject'                             => 'Collectionstock – Your New Password',
    'password.message'                             => 'This e-mail is being sent because your password has been changed. If you did not request this change, please contact us at <a href="mailto:help@collectionstock.com">help@collectionstock.com</a>',

    // user activate
    'activate.subject'                             => 'Welcome, Collectionstock User!',
    'activate.greet'                               => 'We have successfully received your registration to become a Collectionstock User.',
    'activate.ready'                               => 'Since you have not activated your account yet, we help you complete the activation already. You are now ready to look for cool commercial designs for your products at <a href="https://collectionstock.com">Collectionstock.com</a>!',

    // user inactivate
    'inactivate.subject'                             => 'Your account is inactivated',
    'inactivate.info'                                => 'On your specific request we inactivated your account at Collectionstock. You can not use our services anymore and all your data will be automatically erased from our system within 30 days.',
    'inactivate.undo'                                => 'If this is an error or you change your mind, we can always re-activate your account if you contact us at help@collectionstock.com before :erased_at.
    <br><br>If not, it will be a goodbye forever.',

    // common
    'common.or'           => 'or',
    'common.library'      => 'Collectionstock Library',
    'common.premium'      => 'Collectionstock Premium',

    'libs.mo'              => 'Monthly',
    'libs.yr'              => 'Yearly',
    'mo'                   => 'monthly',
    'yr'                   => 'yearly',

    // plan payment
    'libs.recharge'                              => 'Recharge Subscription',
    'libs.payment_settle_days'                   => 'Please settle the next month payment within :days days.',
    'libs.payment_settle_plan_days'              => 'We send this email to inform you that the subscription plan :plan requires payment within :days days.',
    'libs.payment_settle_flow'                   => 'Please visit your current subscription page under account to recharge the subscription manually before your plan expire in :date',
    'libs.distributor_settle_flow'               => 'Please contact your distributor to confirm your next year subscription before your plan expire in :date',

    // grace period
    'libs.about_to_teminate'               => 'You subscription will terminate soon!',
    'libs.teminate_plan_date'              => 'We send this email to inform you that your subscription plan :plan is to be terminated at :date . This is due to unsuccessful transaction payment. ',
    'libs.teminate_warning'                => 'Attention: Upon termination of your subscription plan, you wil have no longer access to Collectionstock Library and will not be able to use any of the services on Collectionstock Library. All extended licenses granted to you will automatically terminate and you must immediately cease using the designs.',
    'libs.teminate_check'                  => 'Please check with your payment method, or update the payment details to continue using our services.',

    // subscription start
    'libs.subscription_start'               => 'Welcome to Collectionstock Library!',

    // libs
    'upgrade.from_free' => 'Your Free Trial will be valid until :date. Starter Plan will automatically start and charged at the end of the date. If you want to start immediately or cancel you can go to',
    'upgrade.from_free_manual' => 'Your Free Trial will be valid until :date. If you want to start immediately or cancel you can go to',
    'upgrade.plan'      => 'Subscription Status',
    'upgrade.why'       => '',
    'plan.upgrade_to'   => 'Upgrade to',
    'libs.welcome'      => 'Welcome to Collectionstock Library! You have now started your :plan and you will enjoy the following benefits:',
    'libs.free_benefit' => '
    <ul>
        <li>Get <strong>10 Free Designs</strong> To Download</li>
        <li>Access to our library and trend designs</li>
        <li>Design can be only used for personal use. Commercial use is prohibited.</li>
        <li>Use the Product Simulator & share simulation</li>
    </ul>
    ',
    'libs.free_benefit_manual' => '
    <ul>
        <li>Get <strong>10 Free Designs</strong> To Download</li>
        <li>Access to our library and trend designs</li>
    </ul>
    ',

    // subscription end
    'libs.subscription_end'   => 'Your subscription has terminated.',
    'libs.ended'              => 'We send this email to inform you that your Subscription :plan at Collectionstock has been terminated at :date. You will have no more access to the Collectionstock Library.',
    'libs.licence'            => 'IMPORTANT: all designs you have downloaded from Collectionstock Library can not be used anymore. The extended licence to use the designs is valid only if you have an active subscription.',
    'libs.bye'                => 'We are sorry to see that you are leaving us. But feel free to re-join us anytime!',

    // monthly report
    'libs.monthly_report'   => 'Your Monthly subscription report is ready.',
    'libs.overview_list'    => 'Every month we will send you a report with an overview of the designs you have downloaded on Collectionstock Library during your subscription period. These designs can be used according to the <a href="https://www.collectionstock.com/legal/users/terms-and-conditions
    ">Extended License Terms </a>and only when having a valid and active subscription.',

    // Qouta reminder
    'libs.quota_reminder'   => 'Collectionstock Download Quota Status',
    'libs.remind_quota'     => '
        We would like to update you on your current Collectionstock Library Subscription
        <br><br>
        Your Plan: :plan
        <br>
        Your outstanding download quota: :quota (from :downloads /month)
        <br>
        Next billing date: :expiry_date
        <br><br>
        We kindly remind you outstanding quota will not be transferred on your next billing date. Therefore please use up your download quota before :expiry_date.
    ',

    // Free Trial reminder
    'libs.free_trial_reminder'   => 'REMINDER – YOUR FREE TRIAL WILL EXPIRE SOON',
    'libs.remind_free_trial'     => '
        We would like to remind you that your FREE TRIAL for Collectionstock Library will expire on :date

        <br><br>

        After your Free Trial you will be <strong>upgraded automatically to the Starter Plan at USD$:amount per :period</strong> and we will charge your credit card on a monthly base until you cancel your subscription. <strong>No refund will be possible</strong>.

        <br><br>

        Starter Plan:
        <ul>
            <li>
                USD$ :amount/:period for 50 Design Downloads per month
                <br><br>
                <ul>
                    <li>Extended License</li>
                    <li>High Quality Vectors</li>
                </ul>
            </li>
            <li>Every month more than 400 new trend designs with inspirational moodboards</li>
            <li>Access to our Design Library with more than 15,000 designs</li>
        </ul>

        <br><br>

        <p><small>If you don’t want to be upgraded to a Starter Plan automatically, then go to your <a href="https://www.collectionstock.com/account/lib-plan">Subscription Plan Section</a> under your account <strong>at least one day before the Free Trial expiry date </strong> and follow the steps on the site to unsubscribe. </small></p>
    ',
    'libs.remind_free_trial.manual'                       => '
        We would like to remind you that your FREE TRIAL for Collectionstock Library will expire on :date

        <br><br>

        After your Free Trial You will not be able to access Collectionstock without a subscription. We strongly recommend you to schedule a free demo with your representative, or subscribe a plan now to enjoy all the features offered by Collectionstock.

        <p><small>If you want to subscribe, then go to your <a href="https://www.collectionstock.com/account/lib-plan">Subscription Plan Section</a> under your account and follow the steps on the site to subscribe. </small></p>
    ',

    // creator applied
    'creator.apply.subject'                        => 'Your application as a Studio on Collectionstock Premium',
    'creator.apply.message'                        => 'We have successfully received your application as a Studio on Collectionstock Premium. Our quality team will check carefully your submitted information and will contact you within 4 weeks if your application has been accepted or not.',
    'creator.apply.thank'                          => 'Thanks for your patience and your interest in Collectionstock!',

    // creator approved
    'creator.approve.subject'                      => 'Yes, your application as a Studio on Collectionstock Premium has been accepted!',
    'creator.approve.congrats'                     => 'Congratulations! You have been accepted as a Studio on Collectionstock Premium! As from now you can submit your creative graphic designs and use all Studio Tools.',
    'creator.approve.commission'                   => 'Please note that Collectionstock will charge a Commission of :percentage % for every design you sell and this until further notice.',
    'creator.approve.login'                        => 'To get immediately started and to find more information on how to use all Studio functions,<br><a href="https://www.collectionstock.com/en/account/studio/edit" target="_blank">Log In</a> and go to the Studio Zone in the User Menu!',

    // creator rejected
    'creator.reject.subject'                       => 'Your application as a Studio on Collectionstock Premium has been declined!',
    'creator.reject.heading'                       => 'We are sorry to inform you that your application as a Studio has been declined because of the following reason:',
    'creator.reject.a'                             => '<span style="font-weight: bold">Incorrect passport information</span><br><br>Please <a href="https://collectionstock.com/en/become-a-studio" target="_blank">re-apply</a> and fill in all requested fields correctly to complete the Studio application.',
    'creator.reject.b'                             => '<span style="font-weight: bold">Incorrect business registration information</span><br><br>Please <a href="https://collectionstock.com/en/become-a-studio" target="_blank">re-apply</a> and fill in all requested fields correctly to complete the Studio application.',
    'creator.reject.c'                             => '<span style="font-weight: bold">Incorrect PayPal account information</span><br><br>Please <a href="https://collectionstock.com/en/become-a-studio" target="_blank">re-apply</a> and fill in all requested fields correctly to complete the Studio application.',
    'creator.reject.d'                             => '<span style="font-weight: bold">Your submitted portfolio is not meeting our expectations</span><br><br>Thank you for your interest in our concept and we wish you all the best with your future design activities.',

    // creator group changed
    'group.change.subject'                         => 'Your Studio Group has been Updated!',
    'creator.group.change'                         => 'We would like to inform you that the Collectionstock Commission has been adjusted to :percentage%. <br>This commission will be applied to your designs sold, starting as from now and until further notice.<br><br>To find more information about your current Studio Fee, click <a href="https://www.collectionstock.com/en/account/creator-zone/creator-fee">here</a> or <a href="https://www.collectionstock.com/en/account/creator-zone/creator-fee">Log In</a> and check the Studio Zone in the User Menu!',
    // representative group change
    'repgroup.change.subject'                      => 'Your Representative Group has been Updated!',
    'representative.group.change'                  => 'We would like to inform you that the Collectionstock Commission has been adjusted to :percentage%. <br>This commission will be applied to your designs sold, starting as from now and until further notice.',

    // warning
    'creator.warning.subject'                      => 'Warning, Your Collectionstock Studio account will be suspended',
    'creator.warning.greet'                        => 'Your Collectionstock Studio account will be suspended if you keep to continue submitting designs that:',
    'creator.warning.a'                            => 'Are in violation with our Design Standards (see <a href="https://collectionstock.com/en/legal/creators/terms-and-conditions" target="_blank">Studios Terms and Conditions</a>)',
    'creator.warning.b'                            => 'Have copyright infringement.',
    'creator.warning.c'                            => 'Have already been sold on Collectionstock.',
    'creator.warning.last'                         => 'Please note this is your last warning before account suspension!',

    // suspended
    'creator.suspend.subject'                      => 'Your Collectionstock Studio account has been suspended',
    'creator.suspend.greet'                        => 'Your Collectionstock Studio account has been suspended permanent with immediate effect because of the following reason:',

    // design uploaded

    // design approved
    'design.approve.subject'                       => 'Your design on Collectionstock Premium has been approved',
    'design.congrates'                             => 'Congratulations! Your design',
    'design.congrates.2'                           => 'has been approved and is now available on Collectionstock Premium under your Studio page!',
    'design.with.code'                             => 'Design Name',
    'design.overview.message'                      => "See the overview and follow the status of your submitted and approved designs by going to the Studio Zone in the User menu and checking the <b><a href='https://www.collectionstock.com/account/studio/designs'>Designs</a></b> section.",

    // design declined
    'design.decline.subject'                       => 'Revise your submitted design on Collectionstock Premium',
    'design.decline.heading'                       => 'Your design ',
    'design.decline.heading2'                      => ' has not been approved because of the following reason',
    'design.decline.a'                             => '<b>There is a technical error, mistake or bug in the design file</b><br>Please revise your design and <a href="https://collectionstock.com/en/account/design-upload" target="_blank">re-submit your design</a> for approval by going to the Studio zone in the User menu.',
    'design.decline.b'                             => '<b>Mistakes in the design file</b><br><br>Please revise your design and <a href="https://collectionstock.com/en/account/design-upload" target="_blank">re-submit your design</a> for approval by going to the Studio zone in the User menu.',
    'design.decline.c'                             => '<b>Information missing in design file</b><br><br>Please revise your design and <a href="https://collectionstock.com/en/account/design-upload" target="_blank">re-submit your design</a> for approval by going to the Studio zone in the User menu.',
    'design.decline.d'                             => '<b>Bugs in the design file</b><br><br>Please revise your design and <a href="https://collectionstock.com/en/account/design-upload" target="_blank">re-submit your design</a> for approval by going to the Studio zone in the User menu.',
    'design.decline.e'                             => '<b>You did not use the Collectionstock template</b><br><br>Please revise your design and <a href="https://collectionstock.com/en/account/design-upload" target="_blank">re-submit your design</a> for approval by going to the Studio zone in the User menu.',
    'design.decline.f'                             => 'Please revise your design and <a href="https://collectionstock.com/en/account/design-upload" target="_blank">re-submit your design</a> for approval by going to the Studio zone in the User menu.',
    'design.decline.g'                             => '<b>Are in violation with our Design Standards</b>',
    'design.decline.h'                             => '<b>Have copyright infringement</b><br><br>Please revise your design and <a href="https://collectionstock.com/en/account/design-upload" target="_blank">re-submit your design</a> for approval by going to the Studio zone in the User menu.',
    'design.decline.i'                             => '<b>Have already been sold on Collectionstock</b><br><br>Please revise your design and <a href="https://collectionstock.com/en/account/design-upload" target="_blank">re-submit your design</a> for approval by going to the Studio zone in the User menu.',
    'design.decline.j'                             => '<b>Zip file name is wrong. Zipfile name should be same as Design Name</b><br><br>Please revise your design and <a href="https://collectionstock.com/en/account/design-upload" target="_blank">re-submit your design</a> for approval by going to the Studio zone in the User menu.',
    'design.decline.k'                             => '<b>Zip file does not includes the files you have declared</b><br><br>Please revise your design and <a href="https://collectionstock.com/en/account/design-upload" target="_blank">re-submit your design</a> for approval by going to the Studio zone in the User menu.',
    'design.decline.l'                             => '<b>JPG is not seamless</b><br><br>Please revise your design and <a href="https://collectionstock.com/en/account/design-upload" target="_blank">re-submit your design</a> for approval by going to the Studio zone in the User menu.',
    'design.decline.reupload'                      => 'Please revise your design and <a href="https://collectionstock.com/en/account/design-upload" target="_blank">re-submit your design</a> for approval by going to the Studio zone in the User menu.',

    // design sold
    'design.sold.subject'                          => 'Your design on Collectionstock has been sold!',
    'design.sold.congrats'                         => 'Congratulations! Your design on Collectionstock has been sold.',
    'design.sold.more'                             => 'For more details, please check your <a href="https://collectionstock.com/en/account/sales-report">sales report</a>.',
    'design.sold.footer'                           => 'Payment: Your designs sold will be paid on your Paypal account once per month, latest within 45 days after design has been sold. After payment, we will send you an email with full details for your reference.',

    // gain a gift
    'voucher.subject'                              => 'Collectionstock – You got a Gift!',
    'voucher.dear'                                 => 'Dear :name,',
    'voucher.greeting'                             => 'Your friend bought you a Collectionstock Gift Voucher. With this voucher you can source free vector graphic design(s) on Collectionstock!',
    'voucher.message'                              => 'Message from your friend:',
    'voucher.code'                                 => 'Voucher Code:',
    'voucher.value'                                => 'Voucher Value:',
    'voucher.redeem'                               => 'To start using your gift voucher, just simply <a href="https://www.colelctionstock.com">Log In</a> to Collectionstock or <a href="https://www.colelctionstock.com">Join Us</a> for free if you are not yet a member.',
    'voucher.disclaimer'                           => 'Legal disclaimer: This email has been sent to you by Collectionstock as a gift voucher confirmation from and authorised by :name. If you did not consent to receive this email or it is unwanted, you may send us a mail at <a href="mailto:help@collectionstock.com">help@collectionstock.com</a> to ask us to remove your information from our systems. We will not send you any further communication once you have done this. Your personal information and contact details will not be added to our mailing lists or used for any purpose.',

    // invoice
    'invoice.subject'                              => 'Collectionstock - Your Order',
    'invoice'                                      => 'Invoice',
    'permit'                                       => 'Project Development Permit',
    'studio'                                       => 'Studio',
    'gift.card'                                    => 'Gift Card',
    'gift.card.to'                                 => 'To',
    'gift.card.email'                              => 'Email',
    'credit_card'                                  => 'Credit Card',
    'distributor'                                  => 'Distributor',
    'free_checkout'                                => 'Free Checkout',
    'telex_transfer'                               => 'Telegraphic Transfer',
    'premium_credit'                               => 'Premium Credit',
    'pos'                                          => 'Pay On Site',
    'alipay'                                       => 'Alipay',
    'wechatpay'                                    => 'WeChat Pay',
    'google_pay'                                   => 'Google Pay',
    'coupon.1'                                     => 'Percentage',
    'coupon.2'                                     => 'Fixed amount',
    'invoice.congrats'                             => "Congratulations! Please find the invoice for the design(s) you have purchased. The ownership is truly transferred to you and the designs are not longer available on Collectionstock. Keep this invoice as proof and reference and consult our <a href='https://www.collectionstock.com/legal/users/terms-and-conditions/premium'>User Terms & Conditions Premium</a> for more detailed information",
    'invoice.total'                                => 'Total',
    'invoice.coupon'                               => 'Coupon',
    'invoice.sub_total'                            => 'Sub-Total',
    'invoice.goto_order'                           => 'Go to Order Page',
    'invoice.re-download'                          => 'Please remember that purchased designs can always be re-downloaded online, just simply got to the Download Page in the User Menu. In case you would need an invoice, go to Purchase History in the User Menu, select your preferred Order and click on print Invoice.',
    'invoice.remark1'                              => '* Payments are made by US Dollars (USD) and <a href="https://www.collectionstock.com/en/legal/purchase-agreement">Design Acquisition Agreement</a> applies to this order.',
    'invoice.remark2'                              => '* No VAT applies - For EU customers Reverse Charge applies (VAT 0%).',
    'invoice.remark3'                              => '* On all designs you download and use on Collectionstock Library the <a href="https://www.collectionstock.com/en/legal/users/terms-and-conditions">Extended License Terms</a> will apply.',
    'invoice.applied'                              => 'Gift Card applied:',

    // recurring.invoice
    'recurring.invoice'           => 'Your subscription payment',
    'recurring.goto_payment'      => 'Go to payment',
    'recurring.remark1'           => '* Payment are made in USD',

    'recurring.remark2'           => '* Minimum contract period is 1 year. Collectionstock will <strong>automatically charge the subscription fee</strong> at the next billing date(currently <strong>USD$ :total </strong>) to your payment method on a monthly basis for 1 year contract. The subscription can be cancelled at any time but you will be charged until the next 1 year contract start date. Cancellation should be made 7 days before the next contract start date to avoid additional transaction.',
    'recurring.remark3'           => '* Minimum contract period is 1 year. Collectionstock will <strong>automatically charge the subscription fee</strong> at the next billing date(currently <strong>USD$ :total </strong>) to your payment method on a yearly basis for 1 year contract. Cancellation should be made 7 days before the next contract start date to avoid additional transaction.',
    'recurring.remark4'           => '* Minimum contract period is 1 year. Collectionstock will require you to manually settle the yearly subscription fee before the next next billing date(currently <strong>USD$ :total </strong>) on a yearly basis for 1 year contract. Cancellation should be made 7 days before the next contract start date to avoid additional transaction.',

    'old.recurring.remark2'           => '* Collectionstock will <strong>automatically charge the subscription fee</strong> at the next billing date(currently <strong>USD$ :total </strong>) to your payment method on a monthly basis. The subscription can be cancelled at any time. Any cancellations should be made 7 days before next billing date to avoid additional transaction.',
    'old.recurring.remark3'           => '* Collectionstock will <strong>automatically charge the subscription fee</strong> at the next billing date(currently <strong>USD$ :total </strong>) to your payment method on a yearly basis. Any cancellations should be made 7 days before next billing date to avoid additional transaction.',
    'old.recurring.remark4'           => '* Collectionstock will require you to manually settle the yearly subscription fee before the next next billing date(currently <strong>USD$ :total </strong>) on a yearly basis. Any cancellations should be made 7 days before next billing date to avoid additional transaction.',

    'recurring_type'              => 'Payment Cycle',
    'downloads_per_month'         => 'Downloads / Month',

    'yearly'  => 'Yearly',
    'monthly' => 'Monthly',

    'plan'                     => 'Plan',
    'plan.free'                => 'Free Access',
    'plan.starter'             => 'Starter',
    'plan.starter_yearly'      => 'Starter Yearly',
    'plan.pro'                 => 'Pro',
    'plan.pro_yearly'          => 'Pro Yearly',

    'subscription.goto_library'      => 'Go To Site',
    'subscription.free_ended'        => 'Auto Upgrade from Free Trial to starter plan has failed. This maybe due to unsuccessful payment or cancellation. To enjoy our serivce, we suggest you login to Collectionstock Library and choose any plan you like.',
    'subscription.free_ended_manual'            => 'Thank you for trying our services. Our sales representative will follow up with you on your experience with collectionstock free trial. To enjoy our serivce, we suggest you login to Collectionstock Library and choose any plan you like.',

    // offer sheet and inquiry
    'inquiry.subject'                              => 'Your Product Supply Inquiry',
    'inquiry.received'                             => 'We have received your Product Supply Inquiry. Please find an overview of your request:',
    'inquiry.offer_soon'                           => 'Our merchandisers will send you a detailed offer soon!',
    'inquiry.quantity'                             => 'Quantity',
    'inquiry.country'                              => 'Country/Region',
    'inquiry.name'                                 => 'Your Name',
    'inquiry.contact'                              => 'Your Contact',
    'inquiry.region.1'                             => 'Shanghai China',
    'inquiry.region.2'                             => 'Tuticorin India',
    'offer.subject'                                => 'Offer Sheet',
    'offer.thank'                                  => 'Thank you for your interest in sourcing products with Collectionstock.',
    'offer.goto_purchases'                         => 'Go To My Purchases',
    'inquiry.greeting'                             => '
    <p>Thanks for your interest in Collectionstock Products!</p>
    <p>We send you hereby the quotation and more details of the products of the products you have selected.</p>',
    'inquiry.details'                              => '
    <p>Quality: Products manufactured in BSCI, Audited factories and conform Oeko-Tex 4.</p>
    <p>Payment: 50% deposit - 50% when goods are ready. Courier cost and sample cost will be for customer account.</p>
    <p>Shipping: Prices based on FOB. This means goods will be ready at port of origin for shipment and customer needs to organise his own shipping / insurance / custom formalities at country of destination. If help for shipment booking is required, Collectionstock can assist at an extra administration charge of US$ 200 per shipment.</p>
    <p>How to proceed: Reply to this email (sales@collectionstock.com) if you have any more questions, or you would like to place an order.</p>',
    'inquiry.product'                              => 'Product:',
    'inquiry.code'                                 => 'Design Code:',
    'inquiry.price'                                => 'Price: FOB',
    'inquiry.pcs'                                  => 'pcs:',

    // sales report
    'sales.subject'                                => 'Your monthly Sales Report is ready!',
    'sales'                                        => 'SALES REPORT',
    'sales.heading'                                => 'We hereby send you the monthly Sales Report for your record. Payment has been effected for your designs sold!',
    'sales.footer'                                 => "Sales report can always be re-downloaded online, just simply go to the <a href='https://collectionstock.com/account/sales-report'>SALES REPORT</a> in the Studio Zone Menu, select the month you would like to check and click on print Sales Report.",
    'sales.date'                                   => ':month / :year',
    'sales.remark'                                 => '* Payments are made by US Dollars (USD) and <a href="https://www.collectionstock.com/en/legal/purchase-agreement">Design Acquisition Agreement</a> applies.',

    // representative report
    'representative.subject'                       => 'Your monthly Representative Report is ready!',
    'representative'                               => 'Representative REPORT',
    'representative.heading'                       => 'We hereby send you the monthly Representative Report for your record. Payment has been effected for your designs sold!',
    'representative.footer'                        => "Representative report can always be re-downloaded online, just simply go to the <a href='https://collectionstock.com/account/representative-report'>Representative REPORT</a> in the Studio Zone Menu, select the month you would like to check and click on print Representative Report.",
    'representative.date'                          => ':month / :year',
    'representative.remark'                        => '* Payments are made by US Dollars (USD) and <a href="https://www.collectionstock.com/en/legal/purchase-agreement">Design Acquisition Agreement</a> applies.',

    // free design
    'free.subject'                                 => 'Collectionstock - Free Design of the Week!',
    'free.greet'                                   => 'Your friend :name sends you a FREE DOWNLOAD for the Design of the Week from Collectionstock!',
    'free.message'                                 => 'Message from your friend:',
    'free.click'                                   => 'Click the button below to get the Free Download! Valid until :date',
    'get.now'                                      => 'Get it Now!',
    'free.disclaimer'                              => 'Legal disclaimer: This email has been sent to you by Collectionstock as a Free Download invitation from and authorised by :name. If you did not consent to receive this email or it is unwanted, you may send us a mail at <a href="mailto:help@collectionstock.com">help@collectionstock.com</a> to ask us to remove your information from our systems. We will not send you any further communication once you have done this. Your personal information and contact details will not be added to our mailing lists or used for any purpose.',

    // share design
    'shared.design.short' => ":name sends you a design from Collectionstock",
    'shared.design' => ':name wants to share this design with you from Collectionstock.
    <br><br>
    At Collectionstock you can find thousands of trendy pattern designs to style your products! Register Now to get free access and discover the design services Collectionstock can offer you.
    ',
    'explore.now'   => 'Explore Now!',

    // invite
    'invite.user' => ':name has invited you to join Collectionstock.',
    'invite.join' => ':name has invited you to join Collectionstock.
    <br><br>
    At Collectionstock you can find thousands of trendy pattern designs to style your products! Register Now to get free access and discover the design services Collectionstock can offer you.',
    'join.now'                                      => 'Join Us Now!',

    // share list
    'share.list.subject'                           => 'Collectionstock - Your friend shared a list with you!',
    'share.list.greet'                             => 'Your friend :name just created a list with trendy designs on Collectionstock',
    'share.list.message'                           => 'Personal message from your friend:',
    'share.list.click'                             => 'Click the button below to see the list with selected Graphic Designs.',
    'go.to.list'                                   => 'Go to List',
    'share.list.disclaimer'                        => 'Legal disclaimer: This email has been sent to you by Collectionstock as a Share List invitation from and authorised by :name. If you did not consent to receive this email or it is unwanted, you may send us a mail at <a href="mailto:help@collectionstock.com">help@collectionstock.com</a> to ask us to remove your information from our systems. We will not send you any further communication once you have done this. Your personal information and contact details will not be added to our mailing lists or used for any purpose.',

    // share Collection
    'share.collection.subject'                     => 'Collectionstock - Your friend shared a Collection with you!',
    'share.collection.greet'                       => 'Your friend :name wants to share Collection with Graphic Designs from Collectionstock.com!',
    'share.collection.message'                     => 'Personal message from your friend:',
    'share.collection.click'                       => 'Click the button below to see the Collection with selected Graphic Designs.',
    'go.to.collection'                             => 'Go to Collection',

    //premium apply
    'premium.heading'                              => 'Please Find Below Details of Premium Member Applied',

    //studio
    'studio.applied.subject'                       => 'Your request for Studio access',
    'studio.applied.message'                       => 'Design Studio <strong>:name</strong> has received your request for studio access and is now evaluating your profile.',
    'studio.applied.contact'                       => 'Once we got more info from the Studio, we will contact you immediately.',
    'studio.applied.subject_to_studio'             => 'You received a new request for Studio access',
    'studio.applied_to_studio.message'             => 'Your Studio has received a new Access Request from :name!',
    'check.it.now'                                 => 'Check it now',

    //studio accepted
    'studio.accept.subject'                        => 'Congratulations! You have now access to more unique designs and trends!',
    'studio.accept.message'                        => 'Design Studio <strong>:name</strong> has accepted your request for studio access. Start to explore the exclusive designs Now!',
    'studio.wrong.register'                        => 'If this registration happened without your knowledge or approval, please contact the CS Helpdesk immediately to suspend the account. ',

    // studio rejected
    'studio.reject.subject'                        => 'Your request for Studio access has been declined!',
    'studio.cancel.subject'                        => 'Studio canceled your access',
    'studio.reject.message'                        => 'We are sorry to inform you that Design Studio <strong>:name</strong> has declined your request for studio access.',
    'studio.reject.try'                            => 'You can always try it later another time or request other Design Studio for studio access.',

    // studio invite
    'studio.invite.subject'                        => 'You have been invited for exclusive Studio Access!',
    'studio.invite.message'                        => 'Design Studio <strong>:name</strong> has invited you to visit their designs and trends on Collectionstock!',
    'studio.wrong.invite'                          => 'If this invitation happened without your knowledge or approval, please contact the CS Helpdesk immediately to suspend the account.',

    // Project Request
    'projects.request.applied.subject'             => 'You received a new Design Project Request',
    'projects.request.applied_to_customer.subject' => 'Congratulations! We received your Design Project Application',
    'projects.request.applied.message'             => 'We have received your application for a Design Project with Design Studio <strong>:name</strong>.',
    'package.selected'                             => 'You selected following package',
    'package.price'                                => 'Application Fee',
    'package.design'                               => 'No of Designs to be created',
    'package.revision'                             => 'No of revisions allowed',
    'package.moodboard'                            => 'Moodboard included',
    'package.deliver'                              => 'Delivery date',
    'projects.request.applied.guarantee'           => 'To guarantee further process of this application. We have blocked your credit card for the amount of :price USD. The Studio will study your request within 7 days and once your project has been accepted by the Studio, we will charge your credit card accordingly.',
    'request.applied.studio.intro'                 => 'You have received a new Design Project Request',
    'request.applied.studio.name'                  => 'Name of the Project: :name',
    'go.to.design.project'                         => 'Go to Design Project',
    'request.applied.studio.check'                 => 'Please check carefully all details and confirm the Design Project within 7 days, otherwise the project will be automatically declined with your customer.',

    'projects.request.accepted.subject'            => 'Your Design Project has been accepted',
    'request.accept.intro'                         => 'We are happy to inform you that Design Studio <b>:studio</b> has accepted your Design Project - <b>:project</b>. Once the Design Project is ready, we will notify you via email.',
    'request.accept.charge'                        => 'We have charged your credit card for the amount of :price USD. Details can be found in the invoice.',
    'download.invoice'                             => 'Download Invoice',

    'projects.request.rejected.subject'            => 'Your Design Project request has been declined!',
    'request.reject.intro'                         => 'We are sorry to inform you that the Studio <b>:studio</b> has declined your Design Project - <b>:project</b> because of the following reason:',
    'request.reject.charge'                        => 'Your Design Project has been cancelled and no charges will be made.',
    'request.reject.apply'                         => 'We recommend you to re-apply or to select another Studio for your Design Project.',

    'projects.ready.subject'                       => 'Your Design Project is ready',
    'project.ready.intro'                          => 'We are happy to inform you that your Design Project - <b>:project</b> is ready now!',
    'project.ready.revision'                       => 'Note: For this Design Project revision are allowed and will be possible till :day. Please request revisions before that date, if not designs will be consider accepted.',

    'projects.commented.subject'                   => 'You received a new Comment for your Design Project',
    'projects.commented_to_studio.subject'         => 'You received a new Comment for your Design Project',
    'project.comment.intro'                        => 'You received a Comment for Design Project - <b>:name</b>.',

    'projects.completed.subject'                   => '',
    'projects.item.revised.subject'                => 'You received a Revision for a Design Project',
    'project.revise.intro'                         => 'Your Design has been revised in your Design Project - <b>:name</b>.',

    'projects.item.revise.request.subject'         => '',
    'projects.item.commented.subject'              => '',
    'projects.payment.invoice.subject'             => '',
    'projects.confirm.subject'                     => 'Design Download Confirmation',
    'congratulations'                              => 'Congratulations!',
    'project.confirm.select'                       => 'You selected and downloaded the following design for the Design Project - ',
    'project.confirm.help'                         => 'This design has been granted to you with an exclusive license to use. The ownership is truly transferred to you. Keep this email as proof and reference and consult our <a href="https://collectionstock.com/en/legal/users/terms-and-conditions">Terms and Conditions</a> for more detailed information.',

    'projects.request.reminder.subject'            => 'Design Project Request Reminder',
    'request.reminder.day'                         => 'You have <b>:day more days left</b> to accept a new Design Project Request',
    'request.reminder.check'                       => 'Please check carefully all details and confirm the Design Project within the deadline, otherwise the project will be automatically declined with your customer.',
    'request.reminder.name'                        => 'Name of the Design Project: <b>:name</b>',
    'request.reminder.attention'                   => 'Attention: May we remind you that if projects are declined without valid reason or on a frequent basis, the Studio will have the risk to be suspended.',

    'projects.expiring.subject'                    => 'Revision for your design project is about to expire!',
    'project.expiring.intro'                       => 'On :day You will not be possible to make any revisions for you Design Project - <b>:project</b>.',
    'project.expiring.recommend'                   => 'We recommend you to do the revisions now or to select the designs you want to download.',
    'project.expired.body'                         => 'However your Design Project will not expire and designs created for you will stay available for download.',

    'projects.expired.subject'                     => 'Revision for your Design Project is expired',
    'project.expired.intro'                        => 'It is not possible anymore to request for revision for your Design Project - <b>:name</b>.',

    'request.reject.a'                             => 'Incomplete information',
    'request.reject.b'                             => 'Missing correct reference files',
    'request.reject.c'                             => 'Corrupted reference files',
    'request.reject.d'                             => 'Project does not match with Studio style strategy',

    'distributor.invoice.subject'                  => 'Your Subscription Invoice',
    'distributor.subscription_ending.subject'      => 'Subscription is about to end',

    'discover.now'                                 => 'Discover Now',
    'continue'                                     => 'Discover Now',

    // preference
    'preference.subject'     => 'We have updated your current Newsletter Status',
    'preference.content.no'  => '
        <p>On your request we have updated your current Newsletter Status to:</p>
        <p>INACTIVE</p>
        <p>This means you will not receive the Collectionstock newsletters and latest offers anymore.</p>
        <p>We will keep it this way and respect your choice</p>
        <p>If you however would change your mind and decide to give your consent to receive our newsletters and latest offers, you can always simply login into “Your Account” and make the appropriate selection under “Newsletter Status”</p>
    ',
    'preference.content.yes' => '
        <p>On your request we have updated your current Newsletter Status to:</p>
        <p>ACTIVE</p>
        <p>This means you will from now on receive the Collectionstock newsletters and latest offers.</p>
        <p>If you however would like to opt-out and not receive our newsletters and latest offers, you can always simply login into “Your Account” and make the appropriate selection under “Newsletter Status”</p>
    ',

    'demo.request.subject' => 'Book your Collectionstock demo now!',
    'demo.request.content' => 'We have received your request for a demo presentation. We are very happy that you are interested to know more about Collectionstock. Please choose a date and time by clicking below calendar link to have a personal skype call with us.',
    // 'demo.request.content'  => 'We have received your request for a demo presentation. We are very happy that you are interested to know more about Collectionstock. We will soon have a presonal skype call.',
    'demo.request.book.now' => 'Book Now',

    'good_request_created.subject' => 'A new product request is submitted.',
    'good_request.subject' => 'Your requested product template is ready!',
    'good_request.congrates' => 'Congratulations! Your requested product template is ready now and available to use in your own product simulator.',
    'good_request_rejected.subject' => 'Your requested product is rejected.',
    'good_request_rejected.reason' => 'We are unable to create template for the product image you uploaded. Please follow the guideline to submit again.',
    'good_request.try.now' => 'Try it Now',

    'lib_request_design.subject' => 'Your requested design is ready!',
    'lib_request_design.congrates' => 'Congratulations! Your requested design is ready now and available to download.',
    'lib_request_design.download.now' => 'Download Now',

    'lib_request_created.subject' => 'We have received your design request.',
    'lib_request_created.info' => 'Congratulations! Your requested design is submitted now and we will make exclusive designs for you based on your briefing.',

    'lib_request_rejected.subject' => 'Your design request has been rejected.',
    'lib_request_rejected.info' => 'Sorry, we cannot make exclusive designs based on your current briefing. Reason:',

    'we_received_to_create_design'=>'asd',
    // common
    'hi'                                                 => 'Hi!',
    'hi_with_name'                                       => 'Hi :name!',
    'design'                                             => 'DESIGN',
    'design_p'                                           => 'Design',
    'items'                                              => 'ITEMS',
    'order'                                              => 'ORDER',
    'order_id'                                           => 'Order ID',
    'amount'                                             => 'Amount',
    'discount'                                           => 'Discount',
    'total'                                              => 'Total',
    'order_date'                                         => 'ORDER DATE',
    'start_date'                                         => 'START DATE',
    'payment_date'                                       => 'PAYMENT DATE',
    'next_billing_at'                                    => 'NEXT BILLING DATE',
    'payment_reference'                                  => 'PAYMENT REFERENCE',
    'code'                                               => 'Design Name',
    'usage'                                              => 'USAGE',
    'usage.type'                                         => 'USAGE TYPE',
    'usage.product'                                      => 'Exclusive',
    'usage.product-licence'                              => 'Non-exclusive',
    'creator_code'                                       => 'STUDIO',
    'selling_price'                                      => 'SELLING PRICE',
    'selling_price_p'                                    => 'Selling Price',
    'cs_commission'                                      => 'CS COMMISSION',
    'cs_commission_p'                                    => 'CS Commission',
    'commission'                                         => 'COMMISSION',
    'commission_p'                                       => 'Commission',
    'creator_fee'                                        => 'STUDIO FEE',
    'creator_fee_p'                                      => 'Studio Fee',
    'representative_fee'                                 => 'Representative Fee',
    'important'                                          => 'IMPORTANT',
    'grand_total'                                        => 'GRAND TOTAL',
    'date'                                               => 'DATE',
    'month'                                              => 'MONTH',
    'company_name'                                       => 'COMPANY NAME',
    'vat'                                                => 'VAT NUMBER',
    'first_name'                                         => 'First Name',
    'last_name'                                          => 'Last Name',
    'billing_address'                                    => 'BILLING ADDRESS',
    'payment_method'                                     => 'PAYMENT METHOD',
    'transaction_id'                                     => 'TRANSACTION ID',
    'paid'                                               => 'PAID',
    'email'                                              => 'EMAIL',
    'yes'                                                => 'YES',
    'no'                                                 => 'NO',
    'skype_id'                                           => 'Skype ID',
    'wechat'                                             => 'Wechat ID',
    'moble'                                              => 'Mobile',
    'country'                                            => 'Country / Region',
    'footer'                                             => "Please do not reply to this email since it is from an automated mailbox. We are unable to respond to your queries via return email.<br><br>
            The information contained in this e-mail is private and confidential and intended solely for the specified addressee. If you are not the addressee and received this e-mail in error, please e-mail to <a href='mailto:help@collectionstock.com' target='_blank'>help@collectionstock.com</a> and destroy this e-mail without using, sending or storing the e-mail or information contained herein.<br><br>
            " . date('Y') . ' &copy; Collectionstock',
    'ending'                                       => "Warm Regards <br> <a href='https://collectionstock.com'>Collectionstock</a>",

    // contacts
    'contacts.type.1'                              => 'User Support',
    'contacts.type.2'                              => 'Studio Support',
    'contacts.type.3'                              => 'Purchase Support',
    'contacts.type.4'                              => 'Press Kit & Contact',
    'contacts.type.5'                              => 'Marketing Contact',
    'contacts.type.6'                              => 'Recruitment Contact',
    'contacts.type.7'                              => 'Legal Contact',
    'contacts.type.8'                              => 'General Contact',

    'contacts.email.1'                             => 'help@collectionstock.com',
    'contacts.email.2'                             => 'help@collectionstock.com',
    'contacts.email.3'                             => 'help@collectionstock.com',
    'contacts.email.4'                             => 'press@collectionstock.com',
    'contacts.email.5'                             => 'marketing@collectionstock.com',
    'contacts.email.6'                             => 'recruitment@collectionstock.com',
    'contacts.email.7'                             => 'legal@collectionstock.com',
    'contacts.email.8'                             => 'help@collectionstock.com',
];
