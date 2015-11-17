#!/usr/bin/env python3
import xml.etree.ElementTree as ET
import os
import random
from urllib import request
import json
import urllib


GOOGLE_IMAGES_URL 	= "http://ajax.googleapis.com/ajax/services/search/images?v=1.0&imgsz=large&rsz=8&q="
EN_SEARCH_PREFIX 	= "film poster"
RU_SEARCH_PREFIX 	= "фильм постер"
BLOCKED_RESOURCES 	= [
	"www.impawards.com",
	"en.wikipedia.org"]
XML_PATH = "/../../users/1/films.xml"
CSS_PATH = "/../../css/style.css"
CSS_FILE = open(os.getcwd()+CSS_PATH).read()

error = ""
h1_title = ""
kinopoisk = ""
imdb = ""
image_url = ""


#https://docs.python.org/3/library/xml.etree.elementtree.html
xmlData = ET.parse(os.getcwd()+XML_PATH)
movies = xmlData.findall("film")

rand = random.randrange(0, len(movies)-1)
random_movie = movies[rand]

search_movie_title = ""
if random_movie.find("en") in random_movie:
	search_movie_title += random_movie.find("en").text+" "
	h1_title += random_movie.find("en").text+" | "

if random_movie.find("ru") in random_movie:
	search_movie_title += random_movie.find("ru").text+" "
	h1_title += random_movie.find("ru").text+" | "

if random_movie.find("year") in random_movie:
	search_movie_title += random_movie.find("year").text+" "
	h1_title += random_movie.find("year").text

#if (h1_title[len(h1_title)-2:] == "| ") { h1_title = h1_title[:len(h1_title)-2] }
search_movie_title += EN_SEARCH_PREFIX

if search_movie_title != "":
	#req = request.urlopen(GOOGLE_IMAGES_URL+search_movie_title)
	#encoding = req.headers.get_content_charset()
	#obj = json.loads(req.read().decode(encoding))

	#image_url = filterFromBlockedResources(jsonMap)
	
	#response = requests.get(GOOGLE_IMAGES_URL+search_movie_title, verify=True)


	if random_movie.find("kinopoisk") in random_movie:
		kinopoisk = random_movie.find("kinopoisk").text

	if random_movie.find("imdb") in random_movie:
		imdb = random_movie.find("imdb").text
else:
	error = "Movie ["+str(rand)+"] - not found!"


print("Content-type: text/html")
print()
print(open(os.getcwd()+"/index.html").read() % (CSS_PATH, CSS_FILE, error, h1_title, h1_title, h1_title))

#print(GOOGLE_IMAGES_URL+urllib.parse.unquote(search_movie_title))