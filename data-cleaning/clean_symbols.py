file1 = open('security-names-raw.txt', 'r')
file2 = open('security-names-cleaned.txt', 'w') 
Lines = file1.readlines()
cleanedLines = [""] * len(Lines); 
count = 0
# Strips the newline character 
for line_index, line in enumerate(Lines): 
    splitLine = line.split(" ")
    fileName = splitLine[-1].split("_")[0]
    cleanedLines[line_index] = fileName + '\n'
file2.writelines(cleanedLines)
file1.close()
file2.close()