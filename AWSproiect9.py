from owlready2 import *

def get_anm_coresp():
    anm_corresp = {}
    anm_list = []

    anm_list.append("glydiazinamide")

    return (anm_corresp, anm_list)


if __name__ == "__main__":
    (anm_corresp, anm_list) = get_anm_coresp()
    onto = get_ontology("file://DINTO.owl").load()
    with onto:
        class is_prescribed(AnnotationProperty):
            pass
        pharmacological_entity = onto.search_one(label="pharmacological entity")
        drugs = list(pharmacological_entity.subclasses())
        for drug in drugs:
            drug.is_prescribed = False
            for d in anm_list:
                if d.lower() in (drug.DBSynonym + drug.Synonym):
                    drug.is_prescribed = True
            print (drug.is_prescribed[0])

        annotations = onto.annotation_properties()