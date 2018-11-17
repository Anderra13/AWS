from owlready2 import *


if __name__ == "__main__":
    onto = get_ontology("file://DINTO.owl").load()

    annotations = onto.annotation_properties()
    for ann in annotations:
        print(ann)