from owlready2 import *


if __name__ == "__main__":
    onto = get_ontology("file://DINTO.owl").load()

    s = "glydiazinamide"
    with onto:
        pharmacological_entity = onto.search_one(label="pharmacological entity")
        drugs = list(pharmacological_entity.subclasses())
        for drug in drugs:
            if s.lower() in ([] + list(drug.DBSynonym) + list(drug.Synonym)):
                print (drug.label)

        annotations = onto.annotation_properties()