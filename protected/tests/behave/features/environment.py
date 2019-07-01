from yaml import load
from selenium import webdriver
import os, time
import pyautogui
import random
import requests
import psycopg2
import json
import ftplib



def before_scenario(context,scenario):
    chrome_options = webdriver.ChromeOptions()
    # psycopg2.connect(user="gigadb",
    #                  password="vagrant",
    #                  host="192.168.10.233",
    #                  port="54321",
    #                  database="gigadb")
    chrome_options.add_argument("--headless")
    chrome_options.add_argument('--lang=en')
    chrome_options.add_argument("--kiosk")
    chrome_options.add_argument('--disable-popup-blocking')
    chrome_options.add_argument("start-maximized")
    context.browser = webdriver.Chrome(executable_path='chromedriver.exe', chrome_options=chrome_options)
    # context.browser = webdriver.Chrome(ChromeDriverManager().install())
    # firefox_options = webdriver.FirefoxOptions()
    # firefox_options.add_argument("start-maximized")
    # context.browser = webdriver.Firefox(executable_path="F:\geckodriver.exe", firefox_options=firefox_options)


def before_feature(context, feature):
        chrome_options = webdriver.ChromeOptions()
        chrome_options.add_argument("--headless")
        chrome_options.add_argument('--lang=en')
        chrome_options.add_argument("--kiosk")
        chrome_options.add_argument('--disable-popup-blocking')
        chrome_options.add_argument("start-maximized")
        context.browser = webdriver.Chrome(executable_path='chromedriver.exe', chrome_options=chrome_options)
        # context.browser = webdriver.Chrome(ChromeDriverManager().install())
        # firefox_options = webdriver.FirefoxOptions()
        # firefox_options.add_argument("start-maximized")
        # context.browser = webdriver.Firefox(executable_path="F:\geckodriver.exe", firefox_options=firefox_options)



def after_step(context, step):
    context.browser.switch_to_window(context.browser.window_handles[-1])
    if step.status == 'failed':
        pyautogui.screenshot(r'bug_screenshot_of_failed_step' + str(random.randint(1,1000)) + '.png')
        send_message = f"https://slack.com/api/chat.postMessage?token=xoxp-138677951075-635388673011-641288946049-ff8e14d335462250e343056a45f8123a&channel=bug_report&text=the failed test step is {step}.&pretty=1"
        custom_header = {"Content-Type": "application/json"}
        requests.post(url=send_message, headers=custom_header)
        # files_upload = f"https://slack.com/api/files.upload?token=xoxp-138677951075-635388673011-641288946049-ff8e14d335462250e343056a45f8123a&channels=bug_report&pretty=1"
        # custom_header = {"Content-Type": "application/json"}
        # requests.post(url=files_upload, headers=custom_header)