import os
directory = './individual_stocks_5yr/'
tradeFile = open('trade-relation.txt', 'w')
dateFile = open('date-relation.txt', 'w')

dates ={}
trades=[]
datesCleanedLines = []
tradesCleanedLines = []

for filename in os.listdir(directory):
    if filename.endswith(".csv"):
        path = (directory + '/' + filename)
        f = open(path)
        lines = f.readlines()
        lines.pop(0) #remove head of csv file with attribute names
        for line_index, line in enumerate(lines):
            line_split = line.split(",")
            date=line_split[0]
            if date not in dates:
                dates[date] = len(dates) + 1
                [year, month, day] = date.split('-')
                datesCleanedLines.append(str(len(dates)) + "," + str(int(month)) + "," + str(int(day)) + "," + str(int(year))+'\n')
            tradesCleanedLines.append(str(dates[date])+"," + line_split[-1].strip()+","+line_split[1] +','+ line_split[2]+','+ line_split[3]+','+line_split[4] +','+line_split[5] + "\n")
dateFile.writelines(datesCleanedLines)
tradeFile.writelines(tradesCleanedLines)