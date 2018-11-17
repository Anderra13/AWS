from owlready2 import *


if __name__ == "__main__":
    onto = get_ontology("file://DINTO.owl").load()

    with onto:
        pharmacological_entity = onto.search_one(label="pharmacological entity")
        #fsdfsd
        print(pharmacological_entity.label)
        drugs = list(pharmacological_entity.subclasses())
        for drug in drugs:
            print(drug.label)

        annotations = onto.annotation_properties()