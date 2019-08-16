import time
import os
from behave import *
from yaml import load
import csv
import psycopg2
import ftplib
from selenium.webdriver.common.keys import Keys


global_user_email = None
global_orc_id = ""
global_link = ""
global_manuscript_link = ""
global_protocol_io_link = ""
global_sketch_fab_link = ""
global_code_oceans_link = ""
global_other_link = ""
global_short_description = ""
global_accession_number = ""
global_database = ""
global_dataset_relationship = ""
global_dataset_doi_relationship = ""
global_project_name = ""
global_first_name = ""
global_last_name = ""
global_middle_name = ""
global_credit = ""
global_funding = None
global_dataset_title = ""
global_dataset_description = ""
global_dataset_manuscript_id = ""
global_author_details = []
global_grant_details = []
global_attributes = []
global_attribute_id = ""
global_ftp_file_names = []
global_ftp_file_sizes = []
global_file_names = []
global_ftp_file_sizes2 = []
global_image_title = ""
global_image_license = ""
global_image_source = ""
global_image_id = ""




# database connection
connection = psycopg2.connect(user="gigadb",
                                     password="vagrant",
                                     host="database",
                                     port="5432",
                                     database="gigadb_test")



def convert_number_to_orcid_format(orcid):
    converted_orcid = '-'.join([orcid[i:i + 4] for i in range(0, len(orcid), 4)])
    return converted_orcid

def wait_for_css_element(context, time_sec, css_element):
    i = 0
    status = False
    while i <= time_sec and status != True:
        try:
            context.browser.find_element_by_css_selector(css_element)
            status = True
        except:
            pass
        time.sleep(1)
        i += 1


def wait_for_xpath_element(context, time_sec, xpath_element):
    time.sleep(0.5)
    i = 0
    status = False
    while i <= time_sec and status != True:
        try:
            context.browser.find_element_by_xpath(xpath_element)
            status = True
        except:
            pass
        time.sleep(1)
        i += 1

def wait_on_xpath_element_active(context, time_sec, xpath_element):
    i = 0
    status = True
    while i <= time_sec and status != False:
        try:
            context.browser.find_element_by_xpath(xpath_element)
        except:
            status = False
        time.sleep(1)
        i += 1


def go_to(context, address_without_domen):
    context.settings = load(open('/var/www/protected/tests/behave/features/conf.yaml').read())
    url = context.settings['base_url']
    basic_url = 'http://{}/{}'.format(url,address_without_domen)
    context.browser.get(basic_url)


@given('url address "{text}"')
def step_impl(context, text):
    context.settings = load(open('/var/www/protected/tests/behave/features/conf.yaml').read())
    url = context.settings['base_url']
    # login = context.settings['login']
    # password = context.settings['password']
    basic_url = 'http://{}/'.format(url)
    if 'staging' in url or 'dev' in url:
        context.browser.get(basic_url)
    context.browser.get('http://{}/'.format(url)+text)


@when('I enter email address "{username}"')
def step_impl(context, username):
    xpath_email_address_field = "//input[@id='LoginForm_username']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_email_address_field)
    context.browser.find_element_by_xpath(xpath_email_address_field).send_keys(username)


@when('I enter password "{password}"')
def step_impl(context,password):
    xpath_email_address_field = "//input[@id='LoginForm_password']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_email_address_field)
    context.browser.find_element_by_xpath(xpath_email_address_field).send_keys(password)


@when("I click Login button")
def step_impl(context):
    xpath_email_address_field = "//input[@class='btn background-btn']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_email_address_field)
    context.browser.find_element_by_xpath(xpath_email_address_field).click()



@then('login is successful, the user name appears in the up left corner "{first_name}"')
def step_impl(context,first_name):
    xpath_first_name = "(//a[1])[1]"
    wait_for_xpath_element(context,time_sec=10,xpath_element=xpath_first_name)
    name = ''

    for i in context.browser.find_element_by_xpath(xpath_first_name):
        if i.text:
            name = i.text

    if not (first_name.lower()in name.lower()):
        assert False


@when("I click Submit new dataset button")
def step_impl(context):
    xpath_submit_new_dataset_button = "//a[@href='/datasetSubmission/choose']"
    wait_for_xpath_element(context,time_sec=5,xpath_element=xpath_submit_new_dataset_button)
    context.browser.find_element_by_xpath(xpath_submit_new_dataset_button).click()


@then('the user is redirected to "{text}" page')
def step_impl(context,text):
    xpath_for_header_text = "//div/h2"
    wait_for_xpath_element(context,time_sec=60,xpath_element=xpath_for_header_text)
    header_text = context.browser.find_element_by_xpath(xpath_for_header_text).text
    assert text == header_text


@when("I click View profile link")
def step_impl(context):
    xpath_view_profile_link = "//a[@href='/user/view_profile']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_view_profile_link)
    context.browser.find_element_by_xpath(xpath_view_profile_link).click()


@when('I click "Upload new dataset from spreadsheet" button')
def step_impl(context):
    xpath_upload_newdataset_from_spreadsheet = "//a[@href='/datasetSubmission/upload']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_upload_newdataset_from_spreadsheet)
    context.browser.find_element_by_xpath(xpath_upload_newdataset_from_spreadsheet).click()


@when('I click "Create new dataset online using wizard" button')
def step_impl(context):
    xpath_create_new_dataset_online_using_wisard = "//a[@href='/datasetSubmission/create1']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_create_new_dataset_online_using_wisard)
    context.browser.find_element_by_xpath(xpath_create_new_dataset_online_using_wisard).click()


@when('remember user email on "Your profile page"')
def step_impl(context):
    xpath_user_email = "(//label[@class='profile-label'])[1]"
    wait_for_xpath_element(context,time_sec=5,xpath_element=xpath_user_email)
    remember_user_email = context.browser.find_element_by_xpath(xpath_user_email).text
    global global_user_email
    global_user_email = remember_user_email


@then("Then “submitter” name is auto-filled with my username/email")
def step_impl(context):
    xpath_submitter_field = "//input[@id='email']"
    wait_for_xpath_element(context,time_sec=2,xpath_element=xpath_submitter_field)
    submitter = context.browser.find_element_by_xpath(xpath_submitter_field).get_attribute('value')
    assert global_user_email == submitter


@when('I enter Title "{dataset_title}" on Study tab')
def step_impl(context, dataset_title):
    xpath_dataset_title = "//input[@id='Dataset_title']"
    wait_for_xpath_element(context,time_sec=2,xpath_element=xpath_dataset_title)
    context.browser.find_element_by_xpath(xpath_dataset_title).clear()
    context.browser.find_element_by_xpath(xpath_dataset_title).send_keys(dataset_title)
    global global_dataset_title
    global_dataset_title = dataset_title

@when("I click out of the field")
def step_impl(context):
    xpath_submitter_field = "//input[@id='email']"
    wait_for_xpath_element(context, time_sec=2, xpath_element=xpath_submitter_field)
    context.browser.find_element_by_xpath(xpath_submitter_field).click()


@then(
    'the length warning message appears "{warning_message}"')
def step_impl(context, warning_message):
    xpath_warning_message = "//div[@id='title-warning']"
    wait_for_xpath_element(context,time_sec=4,xpath_element=xpath_warning_message)
    message = context.browser.find_element_by_xpath(xpath_warning_message).text
    assert message == warning_message


@when('I enter Description "{description}" on Study tab')
def step_impl(context, description):
    xpath_description_iframe = "//iframe[@class='cke_wysiwyg_frame cke_reset']"
    wait_for_xpath_element(context,time_sec=5,xpath_element=xpath_description_iframe)
    iframe = context.browser.find_element_by_xpath(xpath_description_iframe)
    wait_for_xpath_element(context,time_sec=10,xpath_element=xpath_description_iframe)
    context.browser.switch_to.frame(iframe)
    context.browser.find_element_by_tag_name("body").send_keys(description)
    context.browser.switch_to_default_content()
    global global_dataset_description
    global_dataset_description = description



@when('select a Type No "{type}" on Study tab')
def step_impl(context, type):
    xpath_type_check_box = "(//input[@type='checkbox'])[{}]".format(type)
    wait_for_xpath_element(context,time_sec=3,xpath_element=xpath_type_check_box)
    context.browser.find_element_by_xpath(xpath_type_check_box).click()


@when('mark "I have read Terms and Conditions" check-box on Study tab')
def step_impl(context):
    xpath_terms_and_conditions_checkbox = "//input[@id='agree-checkbox']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_terms_and_conditions_checkbox)
    element = context.browser.find_element_by_xpath(xpath_terms_and_conditions_checkbox)
    context.browser.execute_script("arguments[0].scrollIntoView();", element)
    context.browser.execute_script("arguments[0].click();", element)


@when("I click Save button on Study tab")
def step_impl(context):
    xpath_save_button= "//input[@id='next-btn']"
    context.browser.find_element_by_xpath(xpath_save_button).click()


@when('mark "If you are unable to provide a suitable image to help..." check-box on Study tab')
def step_impl(context):
    xpath_save_button = "//input[@id='image-upload']"
    context.browser.find_element_by_xpath(xpath_save_button).click()


@then('"{next}" button appears')
def step_impl(context, next):
    xpath_next_button = "//input[@id='next-btn2']"
    wait_for_xpath_element(context,time_sec=10,xpath_element=xpath_next_button)
    next_text = context.browser.find_element_by_xpath(xpath_next_button).get_attribute('value')
    assert next_text == next


@step("I click Next button on Study tab")
def step_impl(context):
    xpath_next_button = "//input[@id='next-btn2']"
    wait_for_xpath_element(context, time_sec=10, xpath_element=xpath_next_button)
    context.browser.find_element_by_xpath(xpath_next_button).click()


@then('"{error}" error message appears')
def step_impl(context,error):
    xpath_error_message = "//div[@class='errorMessage' and contains(text(),'{}')]".format(error)
    wait_for_xpath_element(context,time_sec=5,xpath_element=xpath_error_message)
    message = context.browser.find_element_by_xpath(xpath_error_message).text
    assert message == error


@when("Choose file from file system on 'Upload your dataset metadata from a spreadsheet' page")
def step_impl(context):
    xpath_choose_file = "//input[@class='upload-control']"
    wait_for_xpath_element(context,time_sec=1,xpath_element=xpath_choose_file)
    path_to_file = os.getcwd()+ r"/protected/tests/behave/GigaDBUploadForm-example1.xls"
    context.browser.find_element_by_xpath(xpath_choose_file).send_keys(path_to_file)


@when('I click "Upload New Dataset"')
def step_impl(context):
    xpath_upload_new_dataset_button = "//input[@class='btn-green upload-control']"
    wait_for_xpath_element(context,time_sec=1,xpath_element=xpath_upload_new_dataset_button)
    context.browser.find_element_by_xpath(xpath_upload_new_dataset_button).click()
    time.sleep(10)


@when('I click "Download template spreadsheet" (Excel) button')
def step_impl(context):
    xpath_download_template_spreadsheet_button = "//a[@href='/files/GigaDBUploadForm.xlsx']"
    wait_for_xpath_element(context,time_sec=1,xpath_element=xpath_download_template_spreadsheet_button)
    context.browser.find_element_by_xpath(xpath_download_template_spreadsheet_button).click()


@when('I click "Download template spreadsheet (Open Office)" button')
def step_impl(context):
    xpath_download_template_spreadsheet_open_office_button = "//a[@href='/files/GigaDBUploadForm.ods']"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_download_template_spreadsheet_open_office_button)
    context.browser.find_element_by_xpath(xpath_download_template_spreadsheet_open_office_button).click()


@when('I click "Download Example 1 (Excel)" button')
def step_impl(context):
    xpath_download_example_excel_button = "//a[@href='/files/GigaDBUploadForm-example1.xls']"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_download_example_excel_button)
    context.browser.find_element_by_xpath(xpath_download_example_excel_button).click()


@when('I click "Download Example 1 (Open Office)" button')
def step_impl(context):
    xpath_download_example_open_office_button = "//a[@href='/files/GigaDBUploadForm-example1.ods']"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_download_example_open_office_button)
    context.browser.find_element_by_xpath(xpath_download_example_open_office_button).click()


@when('I enter ORCiD code "{orcid}"')
def step_impl(context, orcid):
    xpath_orcid_field = "//input[@id='js-author-orcid']"
    wait_for_xpath_element(context,time_sec=1,xpath_element=xpath_orcid_field)
    context.browser.find_element_by_xpath(xpath_orcid_field).send_keys(orcid)
    global global_orc_id
    global_orc_id = orcid


@when('I enter First Name "{first_name}"')
def step_impl(context, first_name):
    xpath_first_name_field = "//input[@id='js-author-first-name']"
    wait_for_xpath_element(context,time_sec=1,xpath_element=xpath_first_name_field)
    context.browser.find_element_by_xpath(xpath_first_name_field).send_keys(first_name)



@when('I enter Middle Name "{middle_name}"')
def step_impl(context, middle_name):
    xpath_middle_name_field = "//input[@id='js-author-middle-name']"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_middle_name_field)
    context.browser.find_element_by_xpath(xpath_middle_name_field).send_keys(middle_name)


@when('I enter Last Name "{last_name}"')
def step_impl(context, last_name):
    xpath_last_name_field = "//input[@id='js-author-last-name']"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_last_name_field)
    context.browser.find_element_by_xpath(xpath_last_name_field).send_keys(last_name)


@when('I enter CrediT "{credit}"')
def step_impl(context, credit):
    xpath_credit_field = "//input[@id='js-author-contribution']"
    wait_for_xpath_element(context,time_sec=1,xpath_element=xpath_credit_field)
    context.browser.find_element_by_xpath(xpath_credit_field).send_keys(credit)


@when('select CreadiT form the autocomplete list "{credit_item}"')
def step_impl(context, credit_item):
    xpath_credit_item = "//div[@class='ui-menu-item-wrapper' and contains(text(),'{}')]".format(credit_item)
    wait_for_xpath_element(context,time_sec=1,xpath_element=xpath_credit_item)
    context.browser.find_element_by_xpath(xpath_credit_item).click()


@when("I click Add Author button")
def step_impl(context):
    xpath_add_author_button = "//a[@id='js-add-author']"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_add_author_button)
    context.browser.find_element_by_xpath(xpath_add_author_button).click()


@then("ORCiD format is nnnn-nnnn-nnnn-nnnn")
def step_impl(context):
    xpath_orcid = "//tr/td[contains(text(),'{}')]".format(convert_number_to_orcid_format(global_orc_id))
    wait_for_xpath_element(context,time_sec=5,xpath_element=xpath_orcid)
    orcid = context.browser.find_element_by_xpath(xpath_orcid).text
    assert convert_number_to_orcid_format(global_orc_id) == orcid

    xpath_created_table = "//tr[@class='odd']/td"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_created_table)
    table_content = []
    table = context.browser.find_elements_by_xpath(xpath_created_table)
    for element in table:
        table_content.append(element.text)
    global global_author_details
    table_content2 = table_content[0:4]
    global_author_details = table_content2


@when("Choose CSV or TSV '{file_format}' file from file system on Author tab")
def step_impl(context, file_format):
    xpath_choose_file = "//input[@id='authors']"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_choose_file)
    path_to_file = "/var/www/protected/tests/behave/authors_example.{}".format(file_format)
    context.browser.find_element_by_xpath(xpath_choose_file).send_keys(path_to_file)



@when("I click Add Authors button")
def step_impl(context):
    xpath_add_authors_button = "//a[@id='js-add-authors']"
    wait_for_xpath_element(context,time_sec=1,xpath_element=xpath_add_authors_button)
    context.browser.find_element_by_xpath(xpath_add_authors_button).click()


@when("I click No button for Public data archive links")
def step_impl(context):
    xpath_no_button = "//a[@id='public-links-no']"
    wait_for_xpath_element(context,time_sec=1,xpath_element=xpath_no_button)
    context.browser.find_element_by_xpath(xpath_no_button).click()
    try:
        alert_obj = context.browser.switch_to.alert
        message = alert_obj.text
        if message == "Are you sure you want to delete all items?":
            alert_obj.accept()
        else:
            pass
    except:
        pass

@then('"{header}" block appears')
def step_impl(context, header):
    xpath_block_header = "//h3[contains(text(),'{}')]".format(header)
    wait_for_xpath_element(context,time_sec=1,xpath_element=xpath_block_header)
    text = context.browser.find_element_by_xpath(xpath_block_header).text
    assert text == header


@when("I click Next button on Author tab")
def step_impl(context):
    xpath_next_button = "//a[@class='btn-green js-save-authors' and contains(text(),'Next')]"
    wait_for_xpath_element(context, time_sec=10, xpath_element=xpath_next_button)
    context.browser.find_element_by_xpath(xpath_next_button).click()


@when("I click '{yes_no}' button for Related GigaDB Datasets")
def step_impl(context,yes_no):
    xpath_related_doi_yes_button = "//a[@id='related-doi-{}']".format(yes_no)
    wait_for_xpath_element(context, time_sec=1,xpath_element=xpath_related_doi_yes_button)
    context.browser.find_element_by_xpath(xpath_related_doi_yes_button).click()
    try:
        alert_obj = context.browser.switch_to.alert
        message = alert_obj.text
        if message == "Are you sure you want to delete all items?":
            alert_obj.accept()
        else:
            pass
    except:
        pass


@then('"{text}" button appears on Related GigaDB Datasets block')
def step_impl(context, text):
    xpath_add_related_doi_button = "(//a[@class='btn js-not-allowed'])[2]"
    wait_for_xpath_element(context,time_sec=2,xpath_element=xpath_add_related_doi_button)
    add_related_doi_button_text = context.browser.find_element_by_xpath(xpath_add_related_doi_button).text
    assert add_related_doi_button_text == text


@when("I click Yes button for Public data archive links")
def step_impl(context):
    xpath_yes_button = "//a[@id='public-links-yes']"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_yes_button)
    context.browser.find_element_by_xpath(xpath_yes_button).click()


@then("Database dropdown menu appears")
def step_impl(context):
    xpath_database_dropdown_list = "//select[@class='js-database dropdown-white']/option"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_database_dropdown_list)
    database_drop_down_data = []
    dropdown_list = context.browser.find_elements_by_xpath(xpath_database_dropdown_list)
    for element in dropdown_list:
        database_drop_down_data.append(element.text)
    database_drop_down_data.remove("Please select")
    count = len(database_drop_down_data)
    cursor = connection.cursor()
    ps_select_query = "select DISTINCT prefix from prefix"
    cursor.execute(ps_select_query)
    prefixes = []
    ps_records = cursor.fetchall()
    for row in ps_records:
        prefixes.append(list(row))
    items = len(prefixes)
    assert count == items







@when("I click '{yes_no_button}' button for Project links")
def step_impl(context, yes_no_button):
    xpath_related_doi_yes_no_button = "//a[@id='projects-{}']".format(yes_no_button)
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_related_doi_yes_no_button)
    context.browser.find_element_by_xpath(xpath_related_doi_yes_no_button).click()
    try:
        alert_obj = context.browser.switch_to.alert
        message = alert_obj.text
        if message == "Are you sure you want to delete all items?":
            alert_obj.accept()
        else:
            pass
    except:
        pass


@then("'{button_text}' button with '{project}' dropdown menu appears")
def step_impl(context, button_text, project):
    xpath_add_project_button = "//a[contains(text(),'{}')]".format(button_text)
    xpath_dropdown_list = "//select[@class='js-project dropdown-white']"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_add_project_button)
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_dropdown_list)
    text = context.browser.find_element_by_xpath(xpath_add_project_button).text
    assert text == button_text
    dropdown_name = context.browser.find_element_by_xpath(xpath_dropdown_list).get_attribute('name')
    assert dropdown_name == project


@when('I click "{yes_no_button}" button for "A published manuscript that uses this data"')
def step_impl(context, yes_no_button):
    xpath_related_doi_yes_no_button = "//a[@id='manuscripts-{}']".format(yes_no_button)
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_related_doi_yes_no_button)
    context.browser.find_element_by_xpath(xpath_related_doi_yes_no_button).click()
    try:
        alert_obj = context.browser.switch_to.alert
        message = alert_obj.text
        if message == "Are you sure you want to delete all items?":
            alert_obj.accept()
        else:
            pass
    except:
        pass


@when('I click "{yes_no_button}" button for "Protocols.io link to methods used to generate this data"')
def step_impl(context, yes_no_button):
    xpath_related_doi_yes_no_button = "//a[@id='protocols-{}']".format(yes_no_button)
    wait_for_xpath_element(context, time_sec=2, xpath_element=xpath_related_doi_yes_no_button)
    context.browser.find_element_by_xpath(xpath_related_doi_yes_no_button).click()
    try:
        alert_obj = context.browser.switch_to.alert
        message = alert_obj.text
        if message == "Are you sure you want to delete all items?":
            alert_obj.accept()
        else:
            pass
    except:
        pass


@when('I click "{yes_no_button}" button for "Actionable code in CodeOceans"')
def step_impl(context, yes_no_button):
    xpath_related_doi_yes_no_button = "//a[@id='codes-{}']".format(yes_no_button)
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_related_doi_yes_no_button)
    context.browser.find_element_by_xpath(xpath_related_doi_yes_no_button).click()
    try:
        alert_obj = context.browser.switch_to.alert
        message = alert_obj.text
        if message == "Are you sure you want to delete all items?":
            alert_obj.accept()
        else:
            pass
    except:
        pass


@when('I click "{yes_no_button}" button for "or any other URL to a stable source of data and files directly related to this dataset"')
def step_impl(context, yes_no_button):
    xpath_related_doi_yes_no_button = "//a[@id='sources-{}']".format(yes_no_button)
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_related_doi_yes_no_button)
    context.browser.find_element_by_xpath(xpath_related_doi_yes_no_button).click()
    try:
        alert_obj = context.browser.switch_to.alert
        message = alert_obj.text
        if message == "Are you sure you want to delete all items?":
            alert_obj.accept()
        else:
            pass
    except:
        pass


@then("Next button class '{next_button_class}' becomes active")
def step_impl(context, next_button_class):
    xpath_active_next_button = "(//a[@class='{}'])[2]".format(next_button_class)
    wait_for_xpath_element(context,time_sec=2, xpath_element=xpath_active_next_button)
    button_class = context.browser.find_element_by_xpath(xpath_active_next_button).get_attribute('class')
    assert button_class == next_button_class


@when('I click "{yes_no_button}" button for "SketchFab 3d-Image viewer links"')
def step_impl(context, yes_no_button):
    xpath_related_doi_yes_no_button = "//a[@id='3d_images-{}']".format(yes_no_button)
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_related_doi_yes_no_button)
    context.browser.find_element_by_xpath(xpath_related_doi_yes_no_button).click()
    try:
        alert_obj = context.browser.switch_to.alert
        message = alert_obj.text
        if message == "Are you sure you want to delete all items?":
            alert_obj.accept()
        else:
            pass
    except:
        pass


@when('choose №"{item}" from dropdown list')
def step_impl(context, item):
    xpath_database_dropdown_list = "//select[@class='js-database dropdown-white']"
    xpath_database = "(//select[@class='js-database dropdown-white']/option)[{}]".format(item)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_database)
    database = context.browser.find_element_by_xpath(xpath_database).text
    global global_database
    global_database = database
    wait_for_xpath_element(context, time_sec=2, xpath_element=xpath_database)
    context.browser.find_element_by_xpath(xpath_database_dropdown_list).click()
    context.browser.find_element_by_xpath(xpath_database).click()


@then('"{accession_number}" field appears')
def step_impl(context, accession_number):
    xpath_accession_number = "//label[contains(text(), 'Accession number')]"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_accession_number)
    text = context.browser.find_element_by_xpath(xpath_accession_number).text
    assert text == accession_number



@step("I click Next button on Additional Information tab")
def step_impl(context):
    xpath_active_next_button = "(//a[@class='btn btn-green js-save-additional'])[2]"
    wait_for_xpath_element(context, time_sec=2, xpath_element=xpath_active_next_button)
    element = context.browser.find_element_by_xpath(xpath_active_next_button)
    context.browser.execute_script("arguments[0].click();", element)
    time.sleep(1)


@when('I enter "{manuscript}" manuscript link')
def step_impl(context, manuscript):
    xpath_manuscript_input_field = "(//input[@class='js-ex-link others-input'])[1]"
    wait_for_xpath_element(context, time_sec=2, xpath_element=xpath_manuscript_input_field)
    context.browser.find_element_by_xpath(xpath_manuscript_input_field).send_keys(manuscript)
    global global_link
    global_link = manuscript


@then('the manuscript url is added and External Link Type is "{external_link_type}"')
def step_impl(context, external_link_type ):
    xpath_url = "//tr/td[contains(text(),'{}')]".format(global_link)
    xpath_external_link_type = "//tr/td[contains(text(), 'manuscript')]"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_url)
    content = context.browser.find_element_by_xpath(xpath_url).text
    assert content == global_link
    link_type = context.browser.find_element_by_xpath(xpath_external_link_type).text
    assert link_type == external_link_type


@when("I click Add Link button")
def step_impl(context):
    xpath_add_link_manuscrip_button = "//a[@class='btn js-add-exLink btn-green']"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_add_link_manuscrip_button)
    context.browser.find_element_by_xpath(xpath_add_link_manuscrip_button).click()


@when("I click out of the manuscript field")
def step_impl(context):
    xpath_related_doi_yes_no_button = "//a[@id='projects-no']"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_related_doi_yes_no_button)
    context.browser.find_element_by_xpath(xpath_related_doi_yes_no_button).click()


@when('I provide "{protocols_io}" Protocols.io DOI')
def step_impl(context, protocols_io):
    xpath_protocols_io_field = "(//input[@class='js-ex-link others-input'])[2]"
    wait_for_xpath_element(context, time_sec=2, xpath_element=xpath_protocols_io_field)
    context.browser.find_element_by_xpath(xpath_protocols_io_field).send_keys(protocols_io)
    global global_link
    global_link = protocols_io


@then('the protocol url is added and External Link Type is "{external_link_type}"')
def step_impl(context, external_link_type):
    xpath_url = "//tr/td[contains(text(),'{}')]".format(global_link)
    xpath_external_link_type = "(//tr/td[contains(text(), 'protocol')])[2]"
    wait_for_xpath_element(context, time_sec=5,xpath_element=xpath_url)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_external_link_type)
    content = context.browser.find_element_by_xpath(xpath_url).text
    assert content == global_link
    link_type = context.browser.find_element_by_xpath(xpath_external_link_type).text
    assert link_type == external_link_type


@when('I provide "{sketch_fab}" SketchFab Link')
def step_impl(context, sketch_fab):
    xpath_sketchfab_input_field = "(//input[@class='js-ex-link others-input'])[3]"
    wait_for_xpath_element(context, time_sec=2, xpath_element=xpath_sketchfab_input_field)
    context.browser.find_element_by_xpath(xpath_sketchfab_input_field).send_keys(sketch_fab)
    global global_link
    global_link = sketch_fab


@then('the sketch fab url is added and External Link Type is "{external_link_type}"')
def step_impl(context, external_link_type ):
    xpath_url = "//tr/td[contains(text(),'{}')]".format(global_link)
    xpath_external_link_type = "(//tr/td[contains(text(), '3d image')])"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_url)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_external_link_type)
    content = context.browser.find_element_by_xpath(xpath_url).text
    assert content == global_link
    link_type = context.browser.find_element_by_xpath(xpath_external_link_type).text
    assert link_type == external_link_type


@when('I provide "{code}" CodeOceans')
def step_impl(context, code):
    xpath_code_oceans_input_field = "(//input[@class='js-ex-link others-input'])[4]"
    wait_for_xpath_element(context,time_sec=2,xpath_element=xpath_code_oceans_input_field)
    context.browser.find_element_by_xpath(xpath_code_oceans_input_field).send_keys(code)
    global global_link
    global_link = code


@then('the CodeOcean is added and External Link Type is "{external_link_type}"')
def step_impl(context, external_link_type):
    xpath_url = "//tr/td[contains(text(),'{}')]".format(global_link)
    xpath_external_link_type = "(//tr/td[contains(text(), 'code')])[2]"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_url)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_external_link_type)
    content = context.browser.find_element_by_xpath(xpath_url).text
    assert content == global_link
    link_type = context.browser.find_element_by_xpath(xpath_external_link_type).text
    assert link_type == external_link_type


@when('I provide the DOI or URL: "{doi_url}"')
def step_impl(context, doi_url ):
    xpath_doi_url_input_field = "(//input[@class='js-ex-link'])"
    wait_for_xpath_element(context, time_sec=2, xpath_element=xpath_doi_url_input_field)
    context.browser.find_element_by_xpath(xpath_doi_url_input_field).send_keys(doi_url)
    global global_link
    global_link = doi_url


@when('I enter short description "{short_description}" for DOI or URL')
def step_impl(context, short_description):
    xpath_short_description_field = "//textarea[@class='js-ex-description']"
    wait_for_xpath_element(context,time_sec=2,xpath_element=xpath_short_description_field)
    context.browser.find_element_by_xpath(xpath_short_description_field).send_keys(short_description)
    global global_short_description
    global_short_description = short_description


@then('the DOI or URL is added, Short Description is added and External Link Type is "{external_link_type}"')
def step_impl(context, external_link_type):
    xpath_url = "//tr/td[contains(text(),'{}')]".format(global_link)
    xpath_external_link_type = "(//tr/td[contains(text(), 'source')])"
    xpath_short_description_field = "//tr/td[contains(text(),'{}')]".format(global_short_description)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_url)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_external_link_type)
    wait_for_xpath_element(context,time_sec=5,xpath_element=xpath_short_description_field)
    content = context.browser.find_element_by_xpath(xpath_url).text
    assert content == global_link
    short_description_content = context.browser.find_element_by_xpath(xpath_short_description_field).text
    assert short_description_content == global_short_description
    link_type = context.browser.find_element_by_xpath(xpath_external_link_type).text
    assert link_type == external_link_type


@when('I enter "{accession_number}" an accession number of Public data archive links block')
def step_impl(context, accession_number):
    xpath_accession_number_field = "//input[@class='js-acc-num']"
    wait_for_xpath_element(context,time_sec=5, xpath_element=xpath_accession_number_field)
    context.browser.find_element_by_xpath(xpath_accession_number_field).send_keys(accession_number)
    global global_accession_number
    global_accession_number = accession_number


@then('Link Type and Link are added to the table')
def step_impl(context):
    xpath_link_type = "//tr/td[contains(text(),'{}')]".format(global_database)
    xpath_link = "//tr/td[contains(text(),'{}')]".format(global_accession_number)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_link_type)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_link)
    added_link_type = context.browser.find_element_by_xpath(xpath_link_type).text
    assert added_link_type == global_database.strip()
    added_link = context.browser.find_element_by_xpath(xpath_link).text
    assert added_link == global_accession_number


@when("I click Add Link button to add Access number")
def step_impl(context):
    xpath_add_link_manuscrip_button = "//a[@class='btn js-add-link btn-green']"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_add_link_manuscrip_button)
    context.browser.find_element_by_xpath(xpath_add_link_manuscrip_button).click()


@when('I click Delete this row "{row_number}" button')
def step_impl(context, row_number):
    xpath_delete_row_button = "(//img[@alt='delete this row'])[{}]".format(row_number)
    wait_for_xpath_element(context,time_sec=5, xpath_element=xpath_delete_row_button)
    context.browser.find_element_by_xpath(xpath_delete_row_button).click()


@then('An alert appears "{alert_message}"')
def step_impl(context, alert_message):
    alert_obj = context.browser.switch_to.alert
    message = alert_obj.text
    assert message == alert_message


@step("I click OK button on the alert pop-up")
def step_impl(context):
    alert_obj = context.browser.switch_to.alert
    alert_obj.accept()


@then('The table No"{table_number_on_the_page}" is empty and contains "{empty_table_text}" on Additional Info tab')
def step_impl(context, table_number_on_the_page, empty_table_text):
    xpath_table = "(//tr/td/span[contains(text(),'No results found.')])[{}]".format(table_number_on_the_page)
    wait_for_xpath_element(context,time_sec=5, xpath_element=xpath_table)
    content = context.browser.find_element_by_xpath(xpath_table).text
    assert content == empty_table_text


@when('I choose the item №"{item}" from relationship dropdown list on Related GigaDB Datasets block')
def step_impl(context, item):
    xpath_relationship_dropdown_list = "//select[@class='js-relation-relationship dropdown-white']"
    xpath_relationship_dropdown_item = "(//select[@class='js-relation-relationship dropdown-white']/option)[{}]".format(item)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_relationship_dropdown_list)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_relationship_dropdown_item)
    relationship = context.browser.find_element_by_xpath(xpath_relationship_dropdown_item).text
    context.browser.find_element_by_xpath(xpath_relationship_dropdown_list).click()
    context.browser.find_element_by_xpath(xpath_relationship_dropdown_item).click()
    global global_dataset_relationship
    global_dataset_relationship = relationship


@when('I choose dataset (DOI) "{item}" from relation doi dropdown list on Related GigaDB Datasets block')
def step_impl(context,item):
    xpath_relation_doi_dropdown_list = "//select[@class='js-relation-doi dropdown-white']"
    xpath_relation_doi_dropdown_item = "(//select[@class='js-relation-doi dropdown-white']/option)[{}]".format(item)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_relation_doi_dropdown_list)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_relation_doi_dropdown_item)
    doi_relation = context.browser.find_element_by_xpath(xpath_relation_doi_dropdown_item).text
    context.browser.find_element_by_xpath(xpath_relation_doi_dropdown_list).click()
    context.browser.find_element_by_xpath(xpath_relation_doi_dropdown_item).click()
    global global_dataset_doi_relationship
    global_dataset_doi_relationship = doi_relation




@when("I click Add Related Doi button on Related GigaDB Datasets block")
def step_impl(context):
    xpath_add_related_doi_button = "//a[@class='btn js-add-relation btn-green']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_add_related_doi_button)
    context.browser.find_element_by_xpath(xpath_add_related_doi_button).click()


@then('Related DOI and Relationship are added to the table')
def step_impl(context):
    xpath_related_doi = "//tr/td[contains(text(),'{}')]".format(global_dataset_doi_relationship)
    xpath_relationship = "//tr/td[contains(text(),'{}')]".format(global_dataset_relationship)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_related_doi)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_relationship)
    added_related_doi = context.browser.find_element_by_xpath(xpath_related_doi).text
    assert added_related_doi == global_dataset_doi_relationship
    added_relationship = context.browser.find_element_by_xpath(xpath_relationship).text
    assert added_relationship == global_dataset_relationship


@when('I choose project option "{project_item}" from dropdown list on Project links block')
def step_impl(context, project_item):
    xpath_project_dropdown_list = "//select[@class='js-project dropdown-white']"
    xpath_project_item = "(//select[@class='js-project dropdown-white']/option)[{}]".format(project_item)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_project_dropdown_list)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_project_item)
    project = context.browser.find_element_by_xpath(xpath_project_item).text
    context.browser.find_element_by_xpath(xpath_project_dropdown_list).click()
    context.browser.find_element_by_xpath(xpath_project_item).click()
    global global_project_name
    global_project_name = project


@when("I click Add Project button on Project links block")
def step_impl(context):
    xpath_add_project_button = "//a[@class='btn js-add-project btn-green']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_add_project_button)
    context.browser.find_element_by_xpath(xpath_add_project_button).click()


@then('the project is added to the table')
def step_impl(context):
    xpath_project_name = "//tr/td[contains(text(),'{}')]".format(global_project_name)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_project_name)
    added_project = context.browser.find_element_by_xpath(xpath_project_name).text
    assert global_project_name == added_project


@then('The authors from the file "{file_format}" are added accordingly')
def step_impl(context, file_format):
    with open(f'/var/www/protected/tests/behave/authors_example.{file_format}', 'r') as file:
        rows = csv.reader(file,
                          delimiter=',',
                          quotechar='"')
        data = [data for data in rows]

    xpath_created_table = "//tr[@class='odd']/td"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_created_table)
    table_content = []
    table = context.browser.find_elements_by_xpath(xpath_created_table)
    for element in table:
        table_content.append(element.text)
    table_content.remove("")
    table_content.remove("")

    table = []
    table.append(table_content)

    global global_author_details
    global_author_details = table_content
    assert data == table



@when('I click "{yes_no_button}" on Fundings tab')
def step_impl(context, yes_no_button):
    xpath_yes_no_button = "//a[@id='funding-{}-button']".format(yes_no_button)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_yes_no_button)
    context.browser.find_element_by_xpath(xpath_yes_no_button).click()


@step("I click '{save_next_previous_buttons}' button on Fundings tab")
def step_impl(context, save_next_previous_buttons):
    xpath_active_next_button = "//a[contains(text(), '{}')]".format(save_next_previous_buttons)
    wait_for_xpath_element(context, time_sec=2, xpath_element=xpath_active_next_button)
    context.browser.find_element_by_xpath(xpath_active_next_button).click()
    time.sleep(1)


@when('I enter a program name "{program_name}", the unique reference "{unique_reference}", PI name "{pi_name}" field, and choose a funding body option "{option}" from dropdown list')
def step_impl(context, program_name, unique_reference, pi_name, option):

    xpath_program_name_field = "//input[@id='program_name']"
    wait_for_xpath_element(context, time_sec=2, xpath_element=xpath_program_name_field)
    context.browser.find_element_by_xpath(xpath_program_name_field).send_keys(program_name)

    xpath_grant_reference_field = "//input[@id='grant']"
    wait_for_xpath_element(context, time_sec=2, xpath_element=xpath_grant_reference_field)
    context.browser.find_element_by_xpath(xpath_grant_reference_field).send_keys(unique_reference)

    xpath_pi_name_field = "//input[@id='pi_name']"
    wait_for_xpath_element(context, time_sec=2, xpath_element=xpath_pi_name_field)
    context.browser.find_element_by_xpath(xpath_pi_name_field).send_keys(pi_name)

    xpath_funding_body_dropdown_list = "//select[@class='js-database dropdown-white']"
    xpath_funding_body = "(//select[@class='js-database dropdown-white']/option)[{}]".format(option)
    wait_for_xpath_element(context, time_sec=2, xpath_element=xpath_funding_body_dropdown_list)
    wait_for_xpath_element(context, time_sec=2, xpath_element=xpath_funding_body)
    funding = []
    funding_body = context.browser.find_element_by_xpath(xpath_funding_body).text
    context.browser.find_element_by_xpath(xpath_funding_body_dropdown_list).click()
    context.browser.find_element_by_xpath(xpath_funding_body).click()
    funding.append(funding_body)
    funding.append(program_name)
    funding.append(unique_reference)
    funding.append(pi_name)
    global global_funding
    global_funding = funding


@when("I click Add Link button on Funding tab")
def step_impl(context):
    xpath_add_link_manuscrip_button = "//a[@class='btn btn-green js-add-funding']"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_add_link_manuscrip_button)
    context.browser.find_element_by_xpath(xpath_add_link_manuscrip_button).click()


@then("the grant details are added into the table")
def step_impl(context):
    grant_details = []
    xpath_details_table = "//tr[@class='odd']/td"
    wait_for_xpath_element(context,time_sec=5, xpath_element=xpath_details_table)
    grant_details_table = context.browser.find_elements_by_xpath(xpath_details_table)
    for element in grant_details_table:
        grant_details.append(element.text)
    grant_details.remove("")
    assert global_funding == grant_details
    global global_grant_details
    global_grant_details = grant_details


@when("Choose image file '{image_size}' to upload on Study tab")
def step_impl(context, image_size):
    xpath_choose_file = "//input[@id='Images_image_upload']"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_choose_file)
    path_to_file = os.getcwd() + r"/protected/tests/behave/{}.png".format(image_size)
    context.browser.find_element_by_xpath(xpath_choose_file).send_keys(path_to_file)


@when('I enter Image Title "{image_title}" on Study tab')
def step_impl(context, image_title):
    xpath_image_title_field = "//input[@id='Images_tag']"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_image_title_field)
    context.browser.find_element_by_xpath(xpath_image_title_field).clear()
    context.browser.find_element_by_xpath(xpath_image_title_field).send_keys(image_title)
    global global_image_title
    global_image_title = image_title


@when('choose Image License "{image_license}" drop-down list on Study tab')
def step_impl(context, image_license):
    xpath_image_license_dropdown_list = "//select[@id='Images_license']"
    xpath_image_license_item = "//select/option[contains(text(), '{}')]".format(image_license)
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_image_license_dropdown_list)
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_image_license_item)
    context.browser.find_element_by_xpath(xpath_image_license_dropdown_list).click()
    context.browser.find_element_by_xpath(xpath_image_license_item).click()
    global global_image_license
    global_image_license = image_license


@when('I enter Image Credit "{image_credit}" on Study tab')
def step_impl(context, image_credit):
    xpath_image_credit_field = "//input[@id='Images_photographer']"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_image_credit_field)
    context.browser.find_element_by_xpath(xpath_image_credit_field).clear()
    context.browser.find_element_by_xpath(xpath_image_credit_field).send_keys(image_credit)
    global global_images_photographer
    global_images_photographer = image_credit


@when('I enter Image Source "{image_source}" on Study tab')
def step_impl(context, image_source):
    xpath_image_source_field = "//input[@id='Images_source']"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_image_source_field)
    context.browser.find_element_by_xpath(xpath_image_source_field).clear()
    context.browser.find_element_by_xpath(xpath_image_source_field).send_keys(image_source)
    global global_image_source
    global_image_source = image_source


@when('I login as "{username}" with password "{password}"')
def step_impl(context, username, password):
    xpath_email_address_field = "//input[@id='LoginForm_username']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_email_address_field)
    context.browser.find_element_by_xpath(xpath_email_address_field).send_keys(username)

    xpath_email_address_field = "//input[@id='LoginForm_password']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_email_address_field)
    context.browser.find_element_by_xpath(xpath_email_address_field).send_keys(password)

    xpath_email_address_field = "//input[@class='btn background-btn']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_email_address_field)
    context.browser.find_element_by_xpath(xpath_email_address_field).click()


@when('I go to submission wizard "{text}" URL')
def step_impl(context, text):
    context.settings = load(open('/var/www/protected/tests/behave/features/conf.yaml').read())
    url = context.settings['base_url']
    # login = context.settings['login']
    # password = context.settings['password']
    basic_url = 'http://{}/'.format(url)
    if 'staging' in url or 'dev' in url:
        context.browser.get(basic_url)
    context.browser.get('http://{}/'.format(url) + text)


@given('I am on "{text}" and I login')
def step_impl(context, text):
    context.settings = load(open('/var/www/protected/tests/behave/features/conf.yaml').read())
    url = context.settings['base_url']
    # login = context.settings['login']
    # password = context.settings['password']
    basic_url = 'http://{}/'.format(url)
    if 'staging' in url or 'dev' in url:
        context.browser.get(basic_url)
    context.browser.execute_script("location.reload()")
    context.browser.get('http://{}/'.format(url) + text)
    if 'staging' in url:
        username = "local-gigadb-admin@rijam.ml1.net"
        password = "gigadb"
        xpath_email_address_field = "//input[@id='LoginForm_username']"
        wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_email_address_field)
        context.browser.find_element_by_xpath(xpath_email_address_field).send_keys(username)
        xpath_email_address_field = "//input[@id='LoginForm_password']"
        wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_email_address_field)
        context.browser.find_element_by_xpath(xpath_email_address_field).send_keys(password)
        xpath_email_address_field = "//input[@class='btn background-btn']"
        wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_email_address_field)
        context.browser.find_element_by_xpath(xpath_email_address_field).click()
    elif 'dev' in url:
        username = "test+gigadb345@gigasciencejournal.com"
        password = "gigadb"
        xpath_email_address_field = "//input[@id='LoginForm_username']"
        wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_email_address_field)
        context.browser.find_element_by_xpath(xpath_email_address_field).send_keys(username)
        xpath_email_address_field = "//input[@id='LoginForm_password']"
        wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_email_address_field)
        context.browser.find_element_by_xpath(xpath_email_address_field).send_keys(password)
        xpath_email_address_field = "//input[@class='btn background-btn']"
        wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_email_address_field)
        context.browser.find_element_by_xpath(xpath_email_address_field).click()


@step("A new dataset is created in DB table dataset")
def step_impl(context):
    cursor = connection.cursor()
    postgreSQL_select_Query = "select title, description, manuscript_id from dataset ORDER BY id DESC LIMIT 1"
    cursor.execute(postgreSQL_select_Query)
    dataset_record = []
    dataset_records = cursor.fetchone()
    for row in dataset_records:
        dataset_record.append(row)
    assert global_dataset_title == dataset_record[0]
    assert global_dataset_description in dataset_record[1]
    assert global_dataset_manuscript_id == dataset_record[2]

@step('I enter GigaScience manuscript "{manuscript_id}"')
def step_impl(context, manuscript_id ):
    xpath_dataset_manuscript_id = "//input[@id='Dataset_manuscript_id']"
    wait_for_xpath_element(context, time_sec=2, xpath_element=xpath_dataset_manuscript_id)
    context.browser.find_element_by_xpath(xpath_dataset_manuscript_id).send_keys(manuscript_id)
    global global_dataset_manuscript_id
    global_dataset_manuscript_id = manuscript_id


@step('I click "{save_next_button}" button on Author tab')
def step_impl(context, save_next_button):
    xpath_save_next_button = "//a[@class='btn-green js-save-authors' and contains(text(), '{}')]".format(save_next_button)
    wait_for_xpath_element(context, time_sec=2, xpath_element=xpath_save_next_button)
    context.browser.find_element_by_xpath(xpath_save_next_button).click()
    time.sleep(1)


@step("Author is added to DB author table")
def step_impl(context):
    cursor = connection.cursor()
    postgreSQL_select_Query = "SELECT first_name,middle_name,surname, orcid from AUTHOR ORDER BY id DESC LIMIT 1"
    cursor.execute(postgreSQL_select_Query)
    dataset_record = []
    dataset_records = cursor.fetchone()
    for row in dataset_records:
        dataset_record.append(row)
    assert global_author_details == dataset_record


@when("I delete the added author from DB")
def step_impl(context):
    xpath_delete_row_button = "(//img[@alt='delete this row'])[1]"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_delete_row_button)
    context.browser.find_element_by_xpath(xpath_delete_row_button).click()

    alert_obj = context.browser.switch_to.alert
    alert_obj.accept()

    xpath_save_next_button = "//a[@class='btn-green js-save-authors' and contains(text(), 'Save')]"
    wait_for_xpath_element(context, time_sec=2, xpath_element=xpath_save_next_button)
    context.browser.find_element_by_xpath(xpath_save_next_button).click()


@step("Author is added to DB author table from the file")
def step_impl(context):
    cursor = connection.cursor()
    postgreSQL_select_Query = "SELECT first_name,middle_name,surname, orcid from AUTHOR ORDER BY id DESC LIMIT 1"
    cursor.execute(postgreSQL_select_Query)
    dataset_records = cursor.fetchone()
    assert tuple(global_author_details[:4]) == dataset_records


@then("I delete the added author from DB")
def step_impl(context):
    xpath_delete_row_button = "(//img[@alt='delete this row'])[1]"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_delete_row_button)
    context.browser.find_element_by_xpath(xpath_delete_row_button).click()

    alert_obj = context.browser.switch_to.alert
    alert_obj.accept()

    xpath_save_next_button = "//a[@class='btn-green js-save-authors' and contains(text(), 'Save')]"
    wait_for_xpath_element(context, time_sec=2, xpath_element=xpath_save_next_button)
    context.browser.find_element_by_xpath(xpath_save_next_button).click()


@then("I delete the added author from file in DB")
def step_impl(context):
    cursor = connection.cursor()
    ps_delete_query = "DELETE from AUTHOR WHERE first_name='QA1';"
    cursor.execute(ps_delete_query)
    connection.commit()






@step("the grant details are saved into DB")
def step_impl(context):
    cursor = connection.cursor()
    postgreSQL_select_Query = "select primary_name_display, comments, grant_award, awardee from dataset_funder INNER JOIN funder_name on dataset_funder.funder_id=funder_name.id  ORDER BY dataset_funder.id DESC LIMIT 1"
    cursor.execute(postgreSQL_select_Query)
    grant_records = cursor.fetchone()
    assert tuple(global_grant_details) == grant_records
    cursor.close()


@then("I delete the added grant form DB where program name is '{program_name}'")
def step_impl(context, program_name):
    cursor = connection.cursor()
    delete_query = "DELETE from dataset_funder WHERE comments='{}'".format(program_name)
    cursor.execute(delete_query)
    connection.commit()


@then("the link '{column_name}' is saved to DB '{table}' where dataset id is '{dataset_id}'")
def step_impl(context, column_name, table, dataset_id):
    cursor = connection.cursor()
    select_query = "select {} from {} where dataset_id={} ORDER BY ID DESC LIMIT 1".format(column_name,table,dataset_id)
    cursor.execute(select_query)
    record = cursor.fetchone()
    link = []
    link.append(global_link)
    # if column_name == "identifier":
    #     link.append(global_manuscript_link)
    # elif column_name == "url":
    #     link.append(global_protocol_io_link)
    assert record == tuple(link)


@step("I delete the saved link from DB '{table}' where dataset id is '{dataset_id}'")
def step_impl(context, table, dataset_id):
    cursor = connection.cursor()
    delete_query = "DELETE from {} where dataset_id={}".format(table, dataset_id)
    cursor.execute(delete_query)
    connection.commit()


@when("I click Save button on Additional Information tab")
def step_impl(context):
    xpath_save_button = "//a[@class='btn btn-green js-save-additional' and contains(text(),'Save')]"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_save_button)
    context.browser.find_element_by_xpath(xpath_save_button).click()
    time.sleep(2)


@step("I add some data into Sample table")
def step_impl(context):
    xpath_button = "//a[contains(text(), 'Add Row')]"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_button)
    context.browser.find_element_by_xpath(xpath_button).click()


@step('I select a template №"{option}"')
def step_impl(context, option):
    xpath_dropdown_list = "//select[@class='js-database dropdown-white']"
    xpath_dropdown_option = "(//select[@class='js-database dropdown-white']/option)[{}]".format(option)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_dropdown_list)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_dropdown_option)
    context.browser.find_element_by_xpath(xpath_dropdown_list).click()
    context.browser.find_element_by_xpath(xpath_dropdown_option).click()
    attribute_id = context.browser.find_element_by_xpath(xpath_dropdown_option).get_attribute('value')
    global global_attribute_id
    global_attribute_id = attribute_id



@step("I click '{button}' button")
def step_impl(context, button):
    xpath_button = "//a[contains(text(), '{}')]".format(button)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_button)
    context.browser.find_element_by_xpath(xpath_button).click()
    time.sleep(1)



@then('A pop-up message appears "{message}"')
def step_impl(context, message):
    alert_object = context.browser.switch_to.alert
    alert_message = alert_object.text
    assert message == alert_message


@then("The appropriate template and display new sample table with those columns defined in the template chosen")
def step_impl(context):
    cursor = connection.cursor()
    select_query = "select attribute_name from template_attribute left join attribute on attribute.id = template_attribute.attribute_id where template_attribute.template_name_id = {}".format(global_attribute_id)
    cursor.execute(select_query)
    records = cursor.fetchall()
    xpath_attribute_values = "//input[@class='js-attribute-name-autocomplete ui-autocomplete-input']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_attribute_values)
    attribute_values = context.browser.find_elements_by_xpath(xpath_attribute_values)
    for element in attribute_values:
        assert tuple([element.get_attribute('value')]) in records
    cursor.close()


@step("I choose a valid matadata file to upload on Sample tab")
def step_impl(context):
    xpath_choose_file = "//input[@id='samples']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_choose_file)
    locate_file = os.getcwd()+ r"/protected/tests/behave/sample_example-genomics.csv"
    context.browser.find_element_by_xpath(xpath_choose_file).send_keys(locate_file)

    with open('/var/www/protected/tests/behave/sample_example-genomics.csv', 'r') as file:
        reader = csv.reader(file, delimiter='\t')
        next(reader)
        data = []
        for row in reader:
            data.append(row)
    global global_attributes
    global_attributes = data[0][0]


@then("the metadata is used to populate the sample table")
def step_impl(context):
    xpath_row_records = "//td/input[@type='text']"
    wait_for_xpath_element(context, time_sec=10, xpath_element=xpath_row_records)
    table = context.browser.find_elements_by_xpath(xpath_row_records)
    td_content = []
    for element in table:
        td_content.append(element.get_attribute('value'))
    for row in td_content:
        assert row in global_attributes


@step("I click Upload button on Sample tab")
def step_impl(context):
    xpath_upload_button = "//a[@class='btn btn-green']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_upload_button)
    context.browser.find_element_by_xpath(xpath_upload_button).click()


@then("the user is redirected to Your profile page page")
def step_impl(context):
    text = "Your profile page"
    xpath_for_header_text = "//div/h4"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_for_header_text)
    header_text = context.browser.find_element_by_xpath(xpath_for_header_text).text
    assert text == header_text


@step("a dataset with status “{text}” is included in my user account")
def step_impl(context, text):
    xpath_user_uploading_data = "(//tr[@id='js-dataset-row-393']/td)[5]"
    wait_for_xpath_element(context, time_sec=10, xpath_element=xpath_user_uploading_data)
    status = context.browser.find_element_by_xpath(xpath_user_uploading_data).text
    assert status == text


@step('I click Update button on dataset id "{id}"')
def step_impl(context, id):
    xpath_update_button = "//a[@href='/adminFile/create1/id/{}']/img[@alt='Update']".format(id)
    wait_for_xpath_element(context, time_sec=10, xpath_element=xpath_update_button)
    context.browser.find_element_by_xpath(xpath_update_button).click()


@when("I click “Get File Names” button")
def step_impl(context):
    xpath_get_file_names_button = "//a[@class='btn btn-green js-get-files']"
    wait_for_xpath_element(context, time_sec=10, xpath_element=xpath_get_file_names_button)
    context.browser.find_element_by_xpath(xpath_get_file_names_button).click()
    time.sleep(5)


@step("I have a valid value in FTP username “{username}“")
def step_impl(context, username):
    xpath_username_input = "//input[@id='username']"
    wait_for_xpath_element(context, time_sec=10, xpath_element=xpath_username_input)
    context.browser.find_element_by_xpath(xpath_username_input).send_keys(username)


@step("I have a valid value in FTP password “{password}”")
def step_impl(context, password):
    xpath_password_input = "//input[@id='password']"
    wait_for_xpath_element(context, time_sec=10, xpath_element=xpath_password_input)
    context.browser.find_element_by_xpath(xpath_password_input).send_keys(password)


@then("retrieve the list of files names & sizes from the FTP server ftp://user99@parrot.genomics.cn")
def step_impl(context):
    ftp = ftplib.FTP('parrot.genomics.cn', user='user99', passwd='WhiteLabel')
    file_sizes = []
    file_names = ftp.nlst()
    for name in file_names:
        file_sizes.append(ftp.size(name))
    global global_ftp_file_names
    global_ftp_file_names = file_names
    global global_ftp_file_sizes2
    global_ftp_file_sizes2 = file_sizes
    sizes = []
    for f in file_sizes:
        if f <= 1024:
            sizes.append(f)
        elif f > 1024:
            sizes.append(int(f) / 1024)

    file_sizes2 = []
    for i in sizes:
        file_sizes2.append(round(i, 2))

    l = []
    for i in file_sizes2:
        l.append(str(i))
    global global_ftp_file_sizes
    global_ftp_file_sizes = l


@step("parse file list into table using rules for file extensions")
def step_impl(context):
    xpath_file_name_column = "//td[@style='white-space: nowrap;']"
    wait_for_xpath_element(context, time_sec=10, xpath_element=xpath_file_name_column)
    column_records = context.browser.find_elements_by_xpath(xpath_file_name_column)
    file_names = []
    for element in column_records:
        file_names.append(element.text)
    assert global_ftp_file_names == file_names
    global global_file_names
    global_file_names = file_names

    xpath_file_size_column = "//tr/td[contains(text(),'B')]"
    wait_for_xpath_element(context, time_sec=10, xpath_element=xpath_file_size_column)
    sizes = context.browser.find_elements_by_xpath(xpath_file_size_column)
    file_sizes = []
    for element in sizes:
        file_sizes.append(element.text)
    file_sizes.remove('imagingary_genome_BUSCO_full_results.txt')
    for a, b in zip(file_sizes, global_ftp_file_sizes):
        assert b in a


@step('I click "Return to your profile page" button on Sample tab')
def step_impl(context):
    xpath_return_button = "//a[contains(text(), 'Return to your profile page')]"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_return_button)
    context.browser.find_element_by_xpath(xpath_return_button).click()


@step("recently submitted dataset is highlighted in table")
def step_impl(context):
    xpath_tr = "//tr[@style='background-color: #e3efda;']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_tr)
    background_style = context.browser.find_element_by_xpath(xpath_tr).get_attribute('style')
    style = "background-color: rgb(227, 239, 218);"
    assert background_style == style


@step('I update dataset status to "{status}" where id is "{id}"')
def step_impl(context, status, id):
    cursor = connection.cursor()
    select_query = "UPDATE dataset SET upload_status='{}' where id = {}".format(status, id)
    cursor.execute(select_query)
    connection.commit()


@step('I add a row and enter Sample ID "{sample_id}", Species name "{species_name}" and "{description}"')
def step_impl(context, sample_id, species_name, description):
    xpath_button = "//a[contains(text(), 'Add Row')]"
    xpath_sample_id = "//input[@placeholder='Sample ID']"
    xpath_species_name = "//input[@class='js-species-autocomplete']"
    xpath_description = "//input[@style='width:250px;']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_button)
    context.browser.find_element_by_xpath(xpath_button).click()

    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_sample_id)
    context.browser.find_element_by_xpath(xpath_sample_id).send_keys(sample_id)

    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_species_name)
    context.browser.find_element_by_xpath(xpath_species_name).send_keys(species_name)

    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_description)
    context.browser.find_element_by_xpath(xpath_description).send_keys(description)
    my_records = []
    xpath_table = "//input[@type='text']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_table)
    table_records = context.browser.find_elements_by_xpath(xpath_table)
    for element in table_records:
        my_records.append(element.get_attribute('value'))
    global global_attributes
    global_attributes = my_records
    xpath_entered_sample_id = "//input[@placeholder='Sample ID']"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_entered_sample_id)
    sample_id_name = context.browser.find_element_by_xpath(xpath_entered_sample_id).get_attribute('value')
    global global_attribute_id
    global_attribute_id = sample_id_name



@step('I click on "{save_next_button}" button on Sample tab')
def step_impl(context, save_next_button):
    xpath_save_next_button = "//div[@id='samples-save']/a[contains(text(), '{}')]".format(save_next_button)
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_save_next_button)
    element = context.browser.find_element_by_xpath(xpath_save_next_button)
    context.browser.execute_script("arguments[0].scrollIntoView();", element)
    context.browser.find_element_by_xpath(xpath_save_next_button).click()
    time.sleep(1)


@then("any rows in the sample table are saved to the database")
def step_impl(context):
    cursor = connection.cursor()
    select_query ="select sample.name, species.genbank_name, sample_attribute.value from sample, species, sample_attribute where species.id = sample.species_id and sample_attribute.sample_id = sample.id and sample.name = '{}'".format(global_attribute_id)
    cursor.execute(select_query)
    records = cursor.fetchall()
    l = []
    l.append(tuple(global_attributes))
    assert records == l


@step('dataset upload status is set to "{expected_upload_status}" where dataset_id is "{}"')
def step_impl(context, expected_upload_status, id):
    cursor = connection.cursor()
    select_query = "select upload_status from dataset where id = {}".format(id)
    cursor.execute(select_query)
    record = cursor.fetchall()
    assert expected_upload_status == record[0][0]


@then("the user is redirected to The end page")
def step_impl(context):
    xpath_return_button = "//a[contains(text(), 'Return to your profile page')]"
    wait_for_xpath_element(context, time_sec=50, xpath_element=xpath_return_button)
    text = context.browser.find_element_by_xpath(xpath_return_button).text
    text_on_button = "Return to your profile page"
    assert text == text_on_button


@step("I delete the added sample form DB")
def step_impl(context):
    cursor = connection.cursor()
    delete = "delete from sample where name = '{}'".format(global_attribute_id)
    cursor.execute(delete)
    connection.commit()


@step('I add a second row and enter Sample ID "{sample_id}", Species name "{species_name}" and "{description}"')
def step_impl(context, sample_id, species_name, description):
    xpath_button = "//a[contains(text(), 'Add Row')]"
    xpath_sample_id = "(//input[@placeholder='Sample ID'])[2]"
    xpath_species_name = "(//input[@class='js-species-autocomplete'])[2]"
    xpath_description = "(//input[@style='width:250px;'])[2]"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_button)
    context.browser.find_element_by_xpath(xpath_button).click()

    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_sample_id)
    context.browser.find_element_by_xpath(xpath_sample_id).send_keys(sample_id)

    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_species_name)
    context.browser.find_element_by_xpath(xpath_species_name).send_keys(species_name)

    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_description)
    context.browser.find_element_by_xpath(xpath_description).send_keys(description)


@when("I click Save button on Files tab")
def step_impl(context):
    xpath_save_button = "//input[@class='btn btn-green js-save-files']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_save_button)
    context.browser.find_element_by_xpath(xpath_save_button).click()
    time.sleep(1)


@then("file details are saved to database where dataset_id is '{id}'")
def step_impl(context, id):
    cursor = connection.cursor()
    select_names = "SELECT name FROM file WHERE dataset_id = {} ORDER BY name".format(id)
    cursor.execute(select_names)
    file_names = cursor.fetchall()
    for element in global_file_names:
        assert tuple([element]) in file_names

    description_list = []
    xpath_description = "//textarea[@class='js-description']"
    wait_for_xpath_element(context, 1, xpath_description)
    description = context.browser.find_elements_by_xpath(xpath_description)
    for element in description:
        description_list.append(element.text)
    select_description = "SELECT description FROM file WHERE dataset_id = {} ORDER BY id".format(id)
    cursor.execute(select_description)
    description_records = cursor.fetchall()
    for i in description_list:
        assert tuple([i]) in description_records

    select_sizes = "SELECT size FROM file WHERE dataset_id = {} ORDER BY name".format(id)
    cursor.execute(select_sizes)
    sizes_records = cursor.fetchall()
    for size in global_ftp_file_sizes2:
        assert tuple([size]) in sizes_records






@when("I add description to the files loaded from ftp")
def step_impl(context):
    xpath_description = "//textarea[@class='js-description']"
    wait_for_xpath_element(context, 1, xpath_description)
    description = context.browser.find_elements_by_xpath(xpath_description)
    for element in description:
        element.clear()
        element.send_keys("description " + str(time.ctime()))


@step('I delete the added sample form DB where Sample name is "{}"')
def step_impl(context, name):
    cursor = connection.cursor()
    delete = "delete from sample where name = '{}'".format(name)
    cursor.execute(delete)
    connection.commit()


@when("I remove a description for a file")
def step_impl(context):
    xpath_description_field = "(//textarea[@class='js-description'])[1]"
    wait_for_xpath_element(context, 1, xpath_description_field)
    context.browser.find_element_by_xpath(xpath_description_field).clear()


@step("I click on Complete submission button on Files tab")
def step_impl(context):
    xpath_complete_submission_button = "//input[@class='btn-green js-complete-submission']"
    wait_for_xpath_element(context, 1, xpath_complete_submission_button)
    context.browser.find_element_by_xpath(xpath_complete_submission_button).click()


@then('"{error}" error message appears under the description field on Files tab')
def step_impl(context, error):
    xpath_error_message = "//div[@class='errorMessage']"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_error_message)
    appearing_error_message = context.browser.find_element_by_xpath(xpath_error_message).text
    assert error == appearing_error_message



@step('the status is updated to "{status}" where dataset_id is "{id}"')
def step_impl(context, status, id):
    cursor = connection.cursor()
    select_query = "select upload_status from dataset where id = {}".format(id)
    cursor.execute(select_query)
    record = cursor.fetchall()
    assert tuple([status]) == record[0]


@step("the user is redirected to congratulation page")
def step_impl(context):
    thank_you_message = "Thank you for updating the file metadata and completing the dataset submission. The curatorial team have been notified and will be in touch with details of the next step as soon as they have checked the dataset."
    xpath_thankyou_message = "//p[@style='font-size: 20px;margin-bottom: 15px;']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_thankyou_message)
    message = context.browser.find_element_by_xpath(xpath_thankyou_message).text
    assert thank_you_message == message


@step("I choose a valid matadata file to upload on File tab")
def step_impl(context):
    xpath_choose_file = "//input[@id='files']"
    wait_for_xpath_element(context, time_sec=60, xpath_element=xpath_choose_file)
    locate_file = os.getcwd() + r"/protected/tests/behave/file_metadata.csv"
    context.browser.find_element_by_xpath(xpath_choose_file).send_keys(locate_file)
    time.sleep(2)


@step("I click on Upload button on File tab")
def step_impl(context):
    xpath_upload_button = "//input[@class='btn btn-green']"
    wait_for_xpath_element(context, time_sec=5, xpath_element=xpath_upload_button)
    context.browser.find_element_by_xpath(xpath_upload_button).click()


@then("Data Type is populated accordingly form metadata file")
def step_impl(context):
    xpath_data_type_column = "//select[@class='span2 dropdown-white js-type-id']/option[@selected='selected']"
    wait_for_xpath_element(context, time_sec=1, xpath_element=xpath_data_type_column)
    records = context.browser.find_elements_by_xpath(xpath_data_type_column)

    with open('/var/www/protected/tests/behave/file_metadata.csv', 'r') as file:
        reader = csv.reader(file, delimiter='\t',)
        # next(reader)
        data = []
        for row in reader:
            data.append(row)
        flat_list = []
        for sublist in data:
            for item in sublist:
                flat_list.append(item)
    for element in records:
        assert element.text in ''.join(flat_list)


@step("Description is updated accordingly form metadata file")
def step_impl(context):
    xpath_description = "//textarea[@class='js-description']"
    wait_for_xpath_element(context, 1, xpath_description)
    description = context.browser.find_elements_by_xpath(xpath_description)
    with open('/var/www/protected/tests/behave/file_metadata.csv', 'r') as file:
        reader = csv.reader(file, delimiter='\t',)
        # next(reader)
        data = []
        for row in reader:
            data.append(row)
        flat_list = []
        for sublist in data:
            for item in sublist:
                flat_list.append(item)
    for element in description:
        assert element.text in ''.join(flat_list)


@then('the file is properly saved into DB where dataset id is "{id}"')
def step_impl(context, id):
    cursor = connection.cursor()
    select_image_id = "select image_id from dataset where id={}".format(id)
    cursor.execute(select_image_id)
    image_id = cursor.fetchall()
    i_id = str(image_id[0][0])
    select_tag = "select tag from image where id={}".format(i_id)
    cursor.execute(select_tag)
    tag = cursor.fetchall()
    assert tag[0][0] == global_image_title
    select_license = "select license from image where id={}".format(i_id)
    cursor.execute(select_license)
    license = cursor.fetchall()
    assert license[0][0] == global_image_license
    select_photographer = "select photographer from image where id={}".format(i_id)
    cursor.execute(select_photographer)
    photographer = cursor.fetchall()
    assert photographer[0][0] == global_images_photographer
    select_source = "select source from image where id={}".format(i_id)
    cursor.execute(select_source)
    source = cursor.fetchall()
    assert source[0][0] == global_image_source
    global global_image_id
    global_image_id = i_id


@step("I delete the uploaded image")
def step_impl(context):
    cursor = connection.cursor()
    delete_uploaded_image = "delete from image where id = {}".format(global_image_id)
    cursor.execute(delete_uploaded_image)
    connection.commit()

@then('dataset status is changed to "{expected_dataset_status}" where dataset id is "{dataset_id}"')
def step_impl(context, expected_dataset_status, dataset_id):
    xpath_status = "(//tr[@id='js-dataset-row-{}']/td)[5]".format(dataset_id)
    wait_for_xpath_element(context, 5, xpath_status)
    new_status = context.browser.find_element_by_xpath(xpath_status).text
    assert expected_dataset_status == new_status