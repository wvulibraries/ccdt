#! /usr/bin/env python

class Cluster():

    # Initializer
    def __init__(self,crsDir,crsFileName):
        # Debug Message
        print "Clustering has started"

        # Variable for storing the file path
        self.crsDataFilePath = str(crsDir)+str(crsFileName)

        # Variable for max records
        self.maxRcrds = 100

    # Main function to compute the cluster
    def filterRecrds(self):
        # Main loop to run through records
        with open(self.crsDataFilePath) as crsDataFile:
            for line in crsDataFile:
                print str(line)
                raw_input()

if __name__ == "__main__":
    # Define the path and file
    crsDir = '/mnt/wvcguide/Sen. Rockefeller CSS Archive/'

    # Set the file name
    crsFileName = 'archiving_correspondence.dat'

    # Main method to trigger the clustering
    thisRun = Cluster(crsDir,crsFileName)

    # Start the algorithm
    thisRun.filterRecrds()
