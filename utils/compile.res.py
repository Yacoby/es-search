#compiles resources into single compressed files

import glob, subprocess, hashlib, os

CSS_FILE = '../public/res/css/css.c.css'
JS_FILE = '../public/res/js/js.c.js'
VERSION_FILE = '../config/versions.php'

print "Compiling JavaScript"
fp = open(JS_FILE, 'w+');
for f in glob.glob('../public/res/js_dev/*.js'):
	subprocess.call(['java','-jar','yuicompressor-2.4.2.jar', f],stdout=fp)
fp.close()

print "Compiling CSS"
fp = open(CSS_FILE, 'w+');
for f in glob.glob('../public/res/css_dev/*.css'):
	subprocess.call(['java','-jar','yuicompressor-2.4.2.jar', f],stdout=fp)
fp.close()


print "Generating Hashes"
fp = open(JS_FILE, 'rb');
jsHash = hashlib.md5(fp.read()).hexdigest()
fp.close()

fp = open(CSS_FILE, 'rb');
cssHash = hashlib.md5(fp.read()).hexdigest()
fp.close()

print "Writing Version File"
fp = open(VERSION_FILE, 'w+')
fp.write('<?php\n')
fp.write('define(\'JS_VERSION\', \''+jsHash+'\');\n')
fp.write('define(\'CSS_VERSION\', \''+cssHash+'\');')
fp.close()


print "Generating Documentation"
subprocess.call(
    ['doxygen','utils/doxyfile'],
    cwd='../',
    stdout=open('/dev/null', 'w'),
    stderr=open('doxygen_err', 'w+')
)
