import sys, os

def getFileList(strTop = sys.path[0], bTopDown = False):
    rList = []
    for root, dirs, files in os.walk(strTop, bTopDown):
        root = root.replace(strTop, "", 1)
        root = root.lstrip("\\/")
        for name in files:
            rList.append((os.path.join(root, name)))
    return rList

lic = open("LicenceHeader")
licence = lic.readlines()
lic.close()

fileList = getFileList(sys.path[0] + '/../')
for f in fileList:
    f = '../' + f
    if f.endswith(".php") == False:
        continue

    ifs = open(f)
    lines = ifs.readlines()
    ifs.close()

    if len(lines) > 0:
        if lines[0].find("<?php /* l-b") == 0:
            continue;

	print f

    ofs = open(f, "w")
    for l in licence:
        ofs.write(l)
    ofs.write("\n")

    for l in lines:
       ofs.write(l)
    ofs.close()
