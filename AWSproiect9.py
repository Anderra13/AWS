from owlready2 import *
from xlrd import *
import types


def get_anm_coresp():
    # key = denumire comerciala din anm, value = lista de componente ale medicamentului conform dci din anm
    anm_corresp = {}
    anm_corresp_inv = {}
    # toate componenetele medicamentelor din documentul anm (coloana dci)
    anm_list = []
    # toate componenetele medicamentelor din documentul anm (coloana dci)
    anm_list_combinations = []

    anm_excel = open_workbook("NOM_ANM_PRES_SHEETS.xlsx")

    for i in range(143):
        sheet = anm_excel.sheet_by_index(i)
        num_rows = sheet.nrows  # Number of rows
        if i == 0:
            k = 2
        else:
            k = 1
        for row in range(k, num_rows):  # Iterate through rows
            col0 = sheet.cell(row, 0).value.replace("\n"," ").strip()
            col1 = sheet.cell(row, 1).value.replace("\n"," ").strip()
            if col0 != "":
                anm_corresp[col0] = []
                if "COMBINATII" == col1 or ("COMBINATII" not in col1 and "+" in col1):
                    continue
                if "COMBINATII" in col1:
                    temp = col1.split("(")[1].split(")")[0].split("+")
                    #temp = re.split('(\+)', col1)
                    '''
                    for j in range(0, len(temp)):
                        anm_corresp[col0].append(temp[j])
                        if temp[j] not in anm_list:
                            anm_list.append(temp[j])
                    '''
                    anm_list_combinations.append(col0)
                else:
                    if col1 in anm_corresp_inv.keys():
                        anm_corresp_inv[col1].append(col0)
                    else:
                        anm_corresp_inv[col1] = [col0]
                    anm_corresp[col0].append(col1)
                    if col1 not in anm_list:
                        anm_list.append(col1)

    # anm_list.append("glydiazinamide")

    return (anm_corresp, anm_corresp_inv, anm_list, anm_list_combinations)


def complete_onto(anm_corresp_inv, anm_list, anm_list_combinations):
    onto = get_ontology("file://DINTO.owl").load()
    nr = 0
    with onto:
        class is_prescribed(AnnotationProperty):
            pass
        class den_comerciala(AnnotationProperty):
            pass

        pharmacological_entity = onto.search_one(label="pharmacological entity")
        drugs = list(pharmacological_entity.subclasses())
        for drug in drugs:
            drug.is_prescribed = False
            for d in anm_list:
                if d.lower() in (drug.DBSynonym + drug.Synonym):
                    drug.is_prescribed = True
                    drug.den_comerciala = anm_corresp_inv[d]
                    nr += 1

        for comb_drug in anm_list_combinations:
            new_class = comb_drug.replace(" ", "_")
            NewClass = types.new_class(new_class, (pharmacological_entity,))
            NewClass.label = comb_drug
            NewClass.den_comerciala = comb_drug
            NewClass.is_prescribed = True

    print("nr = ", nr)
    return onto


if __name__ == "__main__":
    (anm_corresp, anm_corresp_inv, anm_list, anm_list_combinations) = get_anm_coresp()
    # print(anm_list_combinations)
    # print(anm_corresp_inv)
    onto = complete_onto(anm_corresp_inv, anm_list, anm_list_combinations)
    onto.save(file="DINTO-modified.owl", format="rdfxml")
