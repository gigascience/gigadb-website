from yaml import load
from selenium import webdriver
import os, time
#import pyautogui
import random
import requests
import psycopg2
import json
import ftplib



def before_scenario(context,scenario):
    chrome_options = webdriver.ChromeOptions()
    psycopg2.connect(user="gigadb",
                     password="vagrant",
                     host="database",
                     port="5432",
                     database="gigadb_test")
    chrome_options.add_argument('--headless')
    chrome_options.add_argument('--no-sandbox')
    chrome_options.add_argument('--disable-dev-shm-usage')
    chrome_options.add_argument('--lang=en')
    chrome_options.add_argument("--kiosk")
    chrome_options.add_argument('--disable-popup-blocking')
    chrome_options.add_argument("start-maximized")
    context.browser = webdriver.Chrome(executable_path='chromedriver', chrome_options=chrome_options)
    # context.browser = webdriver.Chrome(ChromeDriverManager().install())
    # firefox_options = webdriver.FirefoxOptions()
    # firefox_options.add_argument("start-maximized")
    # context.browser = webdriver.Firefox(executable_path="F:\geckodriver.exe", firefox_options=firefox_options)


def before_feature(context, feature):
        chrome_options = webdriver.ChromeOptions()
        chrome_options.add_argument('--headless')
        chrome_options.add_argument('--no-sandbox')
        chrome_options.add_argument('--disable-dev-shm-usage')
        chrome_options.add_argument('--lang=en')
        chrome_options.add_argument("--kiosk")
        chrome_options.add_argument('--disable-popup-blocking')
        chrome_options.add_argument("start-maximized")
        context.browser = webdriver.Chrome(executable_path='chromedriver', chrome_options=chrome_options)
        # context.browser = webdriver.Chrome(ChromeDriverManager().install())
        # firefox_options = webdriver.FirefoxOptions()
        # firefox_options.add_argument("start-maximized")
        # context.browser = webdriver.Firefox(executable_path="F:\geckodriver.exe", firefox_options=firefox_options)
