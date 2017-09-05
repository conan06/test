
def translationChtoEn(list):
    import urllib.request
    import urllib.parse
    import json

    while True:
        content = list
        if content == 'Q':
            break
        else:
            url = 'http://fanyi.youdao.com/translate?smartresult=dict&smartresult=rule&smartresult=ugc&sessionFrom=http://www.youdao.com/'
            data = {}

            data['type'] = 'AUTO'
            data['i'] = content
            data['doctype'] = 'json'
            data['xmlVersion'] = '1.8'
            data['keyfrom'] = 'fanyi.web'
            data['ue'] = 'UTF-8'
            data['action'] = 'FY_BY_CLICKBUTTON'
            data['typoResult'] = 'true'
            data = urllib.parse.urlencode(data).encode('utf-8')
            response = urllib.request.urlopen(url, data)
            #response.add_header("User-Agent", "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.157 Safari/537.36")  
            html = response.read().decode('utf-8')
            target = json.loads(html)
            if target['errorCode'] == 0:
                results = target['translateResult'][0][0]['tgt']
            else:
                results = ''
            # print(results)
            return results
    

#空格替换标点的函数
def replacePunctuations(line):
    for ch in line:
        if ch in "~@#$%^&*()_-+=<>?/,.:;，。：；“”{}[]|\'""":
            line = line.replace(ch, " ")
    return line

def main():
 #用户输入一个文件名
    filename = input("enter a filename:").strip()
    infile = open(filename, "r",encoding='gbk')
    lines=infile.readlines()
    result=open('English_st.txt','w',encoding='gbk')
    for i in range(len(lines)):
        #line=replacePunctuations(line)
        print(translationChtoEn(lines[i]))
        line=translationChtoEn(lines[i])
        result.writelines(line)
        result.writelines('\n')

    # sentence=''.join(line.split())
    # list=sentence
    result.close()
    infile.close()
 
if __name__ == '__main__':
       main()
