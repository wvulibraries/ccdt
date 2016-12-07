#! /usr/bin/env python

# This program is to cluster the records and visualize

class Cluster():

    # Initializer
    def __init__(self,crsFilePath):
        # Debug Message
        print "Clustering has started"

        # Variable for storing the file path
        self.crsDataFilePath = str(crsFilePath)

        # Variable for max records
        self.maxRcrds = 100

        # City Cluster list
        self.cityClusterList = []

        # State Cluster list
        self.stClusterList = []

        # City Count list
        self.cityClusterCntDict = {}

        # State Count list
        self.stClusterCntDict = {}

        # Default values
        self.stDfltList = ["state"]
        self.cityDfltList = ["city","Unknown"]

        # Delimitter
        self.curDlmtr = '\t'

    # Method to split and return the values
    def splitLine(self,line):
        curSpltLst = line.split(self.curDlmtr)
        if len(curSpltLst) == 32:
            return curSpltLst
        else:
            raise ValueError('String identified to not have 32 tokens')

    # Main function to compute the cluster
    # Algorithm:
    # 1. If not in cluster:
    #   1. add cluster key
    #   2. Increament count
    # 2. Else:
    #   1. Increment the key
    def cluster(self):
        # Main loop to run through records
        with open(self.crsDataFilePath) as crsDataFile:
            # Ignore the first line
            # next(crsDataFile)

            # Some variables
            curLine = 0
            prcssd = 0

            # For each record
            for line in crsDataFile:
                # Update variables
                curLine += 1

                # Tokenize the record
                curTknsList = self.splitLine(line)
                curCity = str(curTknsList[12])
                curState = str(curTknsList[13])

                # check for the default values
                if (curCity in self.cityDfltList) | (curState in self.stDfltList):
                    continue

                # Update few variables here:
                prcssd += 1

                # Actual algorithm for city
                # 1. If not in cluster
                if not curCity in self.cityClusterList:
                    #   1. add cluster key
                    self.cityClusterList.append(curCity)
                    #   2. Assign count
                    self.cityClusterCntDict[curCity] = 1
                # 2. Else
                else:
                    self.cityClusterCntDict[curCity]+=1

                # Actual algorithm for state
                # 1. If not in cluster
                if not curState in self.stClusterList:
                    #   1. add cluster key
                    self.stClusterList.append(curState)
                    #   2. Assign count
                    self.stClusterCntDict[curState] = 1
                # 2. Else
                else:
                    self.stClusterCntDict[curState]+=1

                # Debug Statement
                # print "current record: "+str(curLine)+" current city: "+str(curTknsList[12])+" current state: "+str(curTknsList[13])
                # raw_input()

            # Debug statement
            print "Processed: "+str(prcssd)+" City Clusters: "+str(self.cityClusterList)+" City Dict: "+str(self.cityClusterCntDict)+" State Clusters: "+str(self.stClusterList)+" State Dict: "+str(self.stClusterCntDict)

if __name__ == "__main__":
    # Define the path and file
    crsFilePath = '../../testData/test.dat'

    # Main method to trigger the clustering
    thisRun = Cluster(crsFilePath)

    # Start the algorithm
    thisRun.cluster()
