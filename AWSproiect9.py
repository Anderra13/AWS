from owlready2 import *


if __name__ == "__main__":
    onto = get_ontology("file://DINTO.owl").load()

    s = "glydiazinamide"
    with onto:

        class is_prescribed(AnnotationProperty):
            pass

        pharmacological_entity = onto.search_one(label="pharmacological entity")
        drugs = list(pharmacological_entity.subclasses())
        for drug in drugs:
            drug.is_prescribed = False
            if s.lower() in (drug.DBSynonym + drug.Synonym):
                drug.is_prescribed = True
            print (drug.is_prescribed)

        annotations = onto.annotation_properties()