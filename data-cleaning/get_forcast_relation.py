import os
directory = './individual_stocks_5yr/'
forcastFile = open('forcast-relation.txt', 'w')


file1 = open('date-relation.txt','r')
lines = file1.readlines()
dates ={}
for line_index, line in enumerate(lines):
    [dateId, month, day, year] = line.split(",")
    if(len(month) == 1):
        month = '0' + month
    if(len(day) == 1):
        day = '0' + day
    date = year.strip()+'-'+month+'-'+day
    if date not in dates:
        dates[date] = len(dates) + 1

file2 = open('weather2.csv')
lines = file2.readlines()
lines.pop(0) #remove head of csv file with attribute names
cleanedForcastLines = []
for line_index, line in enumerate(lines):

    line_split = line.split(",")[1:-1]
    line_split.pop(2)
    date = line_split[0][1:-1]

    # print(date)
    if date in dates:
        line_split[0] = str(dates[date])
        line_split[1] = line_split[1][1:-1]
        line_split[2]= line_split[2][1:-1]
        line_split[3]= line_split[3][1:-1]
        cleanedForcastLines.append(",".join(line_split) + '\n')

forcastFile.writelines(cleanedForcastLines)


    