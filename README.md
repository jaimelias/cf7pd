# CF7PD

CF7PD is the best Marketing Optimized Wordpress integration plugin for Pipedrive.

CF7PD allows you to create deals, contacts and notes from a single contact form.

CF7PD supports and connects any field for your for deals and contacts, even custom fields.

CF7PD lets you track conversions from digital marketing campaigns in Pipedrive: 

- Adwords
- Social Networks (Facebook, Twitter and Instagram)
- Organic SEO (Search Engine Optimization)
- Email Marketing Campaigns

CF7PD lets you send any data from the HTML or the URL params to Pipedrive.

CF7PD use Cookies to store key decision making data until the form is submitted. This Cookie expires 30 days after it is created.

- Landing page path
- Domain name
- Device type: mobile or desktop
- Language
- Number of visits
- Number of pages visited
- Total time of site (total  visits)
- Time on site (last visit)

CF7PD is integrated with Google's Invisible reCAPTCHA. [Get Your Site Key and Secret Key](https://www.google.com/recaptcha/admin). It is required to replace all  your [submit] tags in your forms with [recaptcha_button].

## User Input Class Selectors

### Date Picker [Pikadate.js](http://amsul.ca/pickadate.js/date/):
- tag: text
- name: any custom field from Pipedrive
- class: datepicker
- example: [text name=PIPEDRIVE_DEAL_0000 class:datepicker]

### Time Picker Picker [Pikadate.js](http://amsul.ca/pickadate.js/date/):

Available languages: DE, ES, ES, FR, IT, JA

- tag: text
- name: any custom field from Pipedrive
- class: timepicker
- example: [text name=PIPEDRIVE_DEAL_0000 class:timepicker]
 
## Country Code List Dropdown: 

Available languages: DE, ES, ES, FR, IT, JA

- tag: select
- name: any custom field from Pipedrive
- class: countrylist
- example: [select name=PIPEDRIVE_PERSON_0000 class:countrylist]


## Hidden Input Class Selectors

The inputs below should be hidden inside a DIV tag: 

```<div style="display: hidden"><input /><input /></div>```

### Landing Channel

The result can be NULL or the referral channel (Organic, Adwords, Instagram, Facebook, Twitter).

- tag: text
- name: any custom field from Pipedrive
- class: channel
- example: 

```[text name=PIPEDRIVE_DEAL_0000 class:channel]```

### Landing Device

There results can be Desktop or Mobile.

- tag: text
- name: any custom field from Pipedrive
- class: device
- example: 

```[text name=PIPEDRIVE_DEAL_0000 class:device]```

### Landing Domain

The result is the websites domain name.

- tag: text
- name: any custom field from Pipedrive
- class: landing_domain
- example: 

```[text name=PIPEDRIVE_DEAL_0000 class:landing_domain]```

### Landing Page Path

The result is the landing page path.

- tag: text
- name: any custom field from Pipedrive
- class: landing_path
- example: 

```[text name=PIPEDRIVE_DEAL_0000 class:landing_path]```
