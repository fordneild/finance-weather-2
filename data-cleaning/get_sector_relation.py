file1 = open('constituents_csv.csv', 'r')
file2 = open('sector-relation.txt', 'w') 
Lines = file1.readlines()
sectors = []
cleanedLines= []
# Strips the newline character 
for line_index, line in enumerate(Lines): 
    sector = line.split(",")[-1]
    if sector not in sectors:
        sectors.append(sector)
        cleanedLines.append(str(len(sectors)) +","+ sector)
    
file2.writelines(cleanedLines)