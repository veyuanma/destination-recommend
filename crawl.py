# -*- coding: utf-8 -*-
#terminal python crawl.py "http://url"
import re
import urllib
import os
import shutil
import sys
import HTMLParser


class Spider_model:
    def _init_(self):
        #os.chdir(r"/Users/zhengqingqing/Desktop/try") # I need change this directory!!!!!!!!!!!
        newpath=r'pic_txt'
        if os.path.exists(newpath):
            shutil.rmtree(newpath)
        self.enable=False
        
    def getHtml(self,url): #get html
	    page=urllib.urlopen(url)
	    html=page.read()
	    return html

    def getHref(self,url): #get all href
        html=self.getHtml(url)
        link_lists=re.findall(r'(?<=href=\").+?(?=\")|(?<=href=\').+?(?=\')',html)
        return link_lists

    def getImg(self,html): #get image
        reg=r'="(((?!>).)+?\.png|((?!>).)+?\.jpg)"' #need modify this to select image
        imgre=re.compile(reg)
        imglist=re.findall(imgre,html)
        x=0
        for imgurl_tuple in imglist:
            imgurl=imgurl_tuple[0]
            print imgurl
            if imgurl[:3]!='htt':
                if imgurl[:2]=='//':
                    imgurl='https:'+imgurl
                else:
                    imgurl=url+'/'+imgurl
            print imgurl
            if imgurl[-3:]=='jpg':
                urllib.urlretrieve(imgurl,'%s.jpg'%x)	
                print 'image %s.jpg done'%x	
            elif imgurl[-3:]=='png':
                urllib.urlretrieve(imgurl,'%s.png'%x)   
                print 'image %s.png done'%x 
            x+=1
            if(x>=30):
                break
        return x
    
    def getTxt(self,html):
        #reg=r'[^>]*% [oO]ff[^<]*'
        #reg=r'(?<=\d)% [oO]ff.+?(?=\s)|\s(.*[dD]iscount.*)\s'
        reg=r'[^>}\"\']*% [oO]ff[^{<\"\']*'
        txtre=re.compile(reg)
        txt=re.findall(txtre,html)
        return txt
    
    def Start(self,url):
        self.enable=True
        html = self.getHtml(url)
        newpath=r'pic_txt'
        if not os.path.exists(newpath):
            os.makedirs(newpath)
            os.chdir(newpath)
        htmllist=self.getHref(url)
        file=open('content.txt','w')
        #capture homepage's text
        datalist=self.getTxt(html)
        for item in datalist:
            file.write(item)
            file.write('\n')
        #capture the next page's text
        count=0
        for item in htmllist:
            if(count<10):
                if item[0]=='h':
                    print item
                    count+=1
                    newhtml=self.getHtml(item)
                    newdatalist=self.getTxt(newhtml)
                    for item in newdatalist:
                        file.write(item)
                        file.write('\n')
        file.close()
        imgCount=self.getImg(html)
        os.chdir(r"/var/www/html/")
        return imgCount

    

myModel=Spider_model()
url=sys.argv[1]
#url="http://tieba.baidu.com/p/2460150866"
print 'Initialling..'
myModel._init_()
print 'Start to capture content in web..'
myModel.Start(url)
print 'Done...check pic_txt folder to get information'
