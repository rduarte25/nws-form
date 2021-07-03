var i = 1;
var sumWeeks = 0;
var weeksArray = [];
var monthsArray = [];
var flatCanAddRow = true;
var susWeeks = 0;
var averageSalary = 0;
var statusSubmit = false;

function calcAverage() {
    var day = new Date().getDate();
    var month = new Date().getMonth()+1;
    var year =  new Date().getFullYear();
    
    if (month < 10) {
        var month = '0' + month 
    } else {
        var month = month 
    }

    if (day < 10) {
        var day = '0' + day;
    } else {
        var day = day;
    }
    jQuery('#method-calc').children().remove();
    jQuery('#btn-method-calc').children().remove()
    var fila = '<div class="form-row"><div class="col-md-6"><label for="">Fecha de movimiento</label><div class="form-group"><input type="date" name="date_salary[]" placeholder="Fecha" required  value="'+year+'-'+month+'-'+day+'" id="date_salary_1" onchange="timeCalc(1)" class="form-control"></div></div><div class="col-md-6"><label for="">Salario base</label><div class="form-group"><input type="number" name="change_salary[]" placeholder="Registra Salario" required step=".01" id="change_salary_1" onchange="timeCalc(1)" class="form-control" min="1"></div></div><div class=""><div class="form-group"><input type="hidden" name="average_salary[]" placeholder="Registra Salario" required step=".01" id="average_salary_1" onchange="timeCalc(1)" class="form-control" min="1"></div></div></div>';
    jQuery("#method-calc").append(fila);

    fila = '<div class="form-row container-btn"><button type="button" id="addRowId" onclick="addRow()">Añadir fila</button><button type="button" id="removeRow">Quitar fila</button></div>';
    jQuery("#btn-method-calc").append(fila);
    document.getElementById("insertAverage").onclick = function() {insertAverage()};
    document.getElementById("removeRow").onclick = function() {removeRow()};
    canRemove();
    canAddRow();
    canSubmit(0);
};


function timeCalc(index) {    
    sumWeeks = 0;
    sumMonths = 0;
    averageSalary = 0;
    var salary = parseFloat(jQuery("#change_salary_"+index).val());
    if(!isNaN(salary)) {
        var indexa = index - 1;
        if(index == 1){
            var begin = moment(jQuery("#date_salary_"+index).val());
        } else {
            var begin = moment(jQuery("#date_salary_"+indexa).val());
        }
        if(index == 1){
            var end = Date.parse(Date("Y-m-d"));
        } else {
            var end = moment(jQuery("#date_salary_"+index).val());
        }
        if( begin > end) {
            var months = begin.diff(end, 'months');
            var weeks = begin.diff(end, 'weeks');
            weeksArray[indexa] = weeks;
            monthsArray[indexa] = months;
            weeksArray.forEach(key => {
                sumWeeks += key;
            });
            monthsArray.forEach(key => {
                sumMonths += key;
            });
            if(sumMonths > 60){
                averageSalary = salary * (months - (monthsWeeks - 60));
            } else {
                averageSalary = salary * months;
            }           
            document.getElementById("average_salary_"+ index).value = averageSalary;
            canSubmit(sumWeeks);           
        }                 
    }
    calcAverageSalary();
    upImms();
    canAddRow();    
}

function calcAverageInput() {
    document.getElementById("average_salary_total").value = parseFloat(jQuery("#average_salary_calc").val());
    canSubmit(0);
}

function canSubmitVerified(sumWeeks){
    if(sumWeeks < 260){
        setTimeout(function(){
            if(sumWeeks < 260){
                Swal.fire(
                    '¡No puede continuar!',
                    '¡Lo sentimos tienes que acumular las 260 semanas de ley!',
                    'error'
                );
            }
        },10000)
        
        document.getElementById('submit').setAttribute('disabled', true);
        document.getElementById('submit').classList.add('disabled'); 
        statusSubmit = false;       
    } else {
        document.getElementById('submit').removeAttribute('disabled');
        document.getElementById('submit').classList.remove('disabled');
        statusSubmit = true;
    }

    var inDemandWeeks = jQuery('#in_demand_weeks').val();
    if(inDemandWeeks < 500){
        setTimeout(function(){
            if(sumWeeks < 260){
                Swal.fire(
                    '¡No puede continuar!',
                    '¡Lo sentimos debes tener mínimo 500 semanas cotizadas!',
                    'error'
                );
            }
        },5000)
        
        document.getElementById('submit').setAttribute('disabled', true);
        document.getElementById('submit').classList.add('disabled'); 
        statusSubmit = false;          
    } else {
        document.getElementById('submit').removeAttribute('disabled');
        document.getElementById('submit').classList.remove('disabled');
        statusSubmit = true;    
    }
}

function canAddRow() {
    var salary = parseFloat(jQuery("#change_salary_"+i).val());
    if(!isNaN(salary) && salary >= 10 && document.getElementById("up_imss").checked) {
        if(sumWeeks < 260) {  
            if(document.getElementById('addRowId') != null) {
                document.getElementById('addRowId').removeAttribute('disabled');
                document.getElementById('addRowId').classList.remove('disabled');
                //document.getElementById('removeRow').classList.remove('btn');
                flatCanAddRow = true; 
            }              
        } else {
            
            if(document.getElementById('addRowId') != null) {
                document.getElementById('addRowId').setAttribute('disabled', true);
                //document.getElementById('removeRow').classList.add('btn');
                document.getElementById('addRowId').classList.add('disabled');
                flatCanAddRow = false;
            }            
        }   
    } else {
        if(document.getElementById('addRowId') != null) {
            document.getElementById('addRowId').setAttribute('disabled', true);
            //document.getElementById('removeRow').classList.add('btn');
            document.getElementById('addRowId').classList.add('disabled');
            flatCanAddRow = false;
        }        
    }    
}

function canSubmit(sumWeeks) {
    var salary = parseFloat(jQuery("#change_salary_"+i).val());
    var inDemandWeeks = parseFloat(jQuery("#in_demand_weeks").val());
    var averageSalaryCalc = parseFloat(jQuery("#average_salary_calc").val());
    if(!isNaN(salary) && salary >= 10 && !isNaN(inDemandWeeks) && inDemandWeeks >= 10 || !isNaN(averageSalaryCalc) && averageSalaryCalc >= 10) {
        document.getElementById('submit').removeAttribute('disabled');
        document.getElementById('submit').classList.remove('disabled');
        statusSubmit = true;
        //document.getElementById('removeRow').classList.remove('btn');
        if(sumWeeks != 0) {
            canSubmitVerified(sumWeeks);
        }        
    } else {
        document.getElementById('submit').setAttribute('disabled', true);
        //document.getElementById('removeRow').classList.add('btn');
        document.getElementById('submit').classList.add('disabled');
        statusSubmit = false;
    }    
}

function addRow(){
    var salary = parseFloat(jQuery("#change_salary_"+i).val());
    if(flatCanAddRow && !isNaN(salary)) {
        i++;
        var day = new Date().getDate();
        var month = new Date().getMonth()+1;
        var year =  new Date().getFullYear();
        
        if (month < 10) {
            var month = '0' + month 
        } else {
            var month = month 
        }

        if (day < 10) {
            var day = '0' + day;
        } else {
            var day = day;
        }

        var fila = '<div id="row_'+i+'"><div class="form-row"><div class="col-md-6"><div class="form-group"><label for="">Fecha de movimiento</label><input type="date" name="date_salary[]" placeholder="Fecha" required  value="'+year+'-'+month+'-'+day+'" id="date_salary_'+i+'" onchange="timeCalc('+i+')" class="form-control"></div></div><div class="col-md-6"><div class="form-group"><label for="">Salario base</label><input type="number" name="change_salary[]" placeholder="Registra Salario" required min="1" step=".01" id="change_salary_'+i+'" onchange="timeCalc('+i+')" class="form-control"></div></div><div class=""><div class="form-group"><input type="hidden" name="average_salary[]" placeholder="Salario Promedio" min="1" required step=".01" id="average_salary_'+i+'" onchange="timeCalc('+i+')" class="form-control" value="0"></div></div></div></div>';
        jQuery("#method-calc").append(fila);
        canRemove(); 
    }
    canAddRow();
    canSubmit(0);
}

function canRemove(){
    if ( i > 1 ) {
        if(document.getElementById('removeRow') != null) {
            document.getElementById('removeRow').removeAttribute('disabled');
            document.getElementById('removeRow').classList.remove('disabled');
            //document.getElementById('removeRow').classList.remove('btn');
        }
    } else {
        if(document.getElementById('removeRow') != null) {
            document.getElementById('removeRow').setAttribute('disabled', true);
            //document.getElementById('removeRow').classList.add('btn');
            document.getElementById('removeRow').classList.add('disabled');
        }
    }
}

function upImms() {
    if(!document.getElementById("up_imss").checked) {
        setTimeout(
            function(){
                if(!document.getElementById("up_imss").checked) {
                    Swal.fire(
                        '¡No puede continuar!',
                        '¡Lo sentimos debes haber cotizado antes 1º Julio de 1997!',
                        'error'
                    )
                }  
            },1000
        );
    }
}

function calcAverageSalary() {
    var weeksTotal = 0;
    var monthsTotal = 0;
    var salaryTotal = 0;
    for (let index = 1; index <= i; index++) {
        if( index < i) {
            indexa = index + 1;
        } else {
            indexa = index;
        }
        
       
        var begin = moment(jQuery("#date_salary_"+index).val());
        if(index == 1){
            var end = Date.parse(Date("Y-m-d"));
        } else {
            var end = moment(jQuery("#date_salary_"+indexa).val());
        }

        var salary = parseFloat(jQuery("#average_salary_"+index).val());
        
        salaryTotal = salaryTotal + salary;
        var days = Math.floor(Math.abs((begin - end) / (24 * 60 * 60 *1000)));
        var weeks = Math.floor(days / 7);
        var months = begin.diff(end, 'months');
        weeksTotal = weeksTotal + weeks;
        monthsTotal = monthsTotal + months;
    }
    //kaverageSalaryTotal = salaryTotal / Math.floor(weeksTotal * 7 / 30);
    averageSalaryTotal = salaryTotal / monthsTotal;
    document.getElementById("average_salary_total").value = averageSalaryTotal;
}

jQuery('#removeRow').on('click', function(e){
    e.preventDefault();
    jQuery("#row_"+i).remove();     
    i--;
    canRemove();
    canAddRow();
    canSubmit(0);
});

function removeRow(){
    jQuery("#row_"+i).remove();     
    i--;
    canRemove();
    canAddRow();
    canSubmit(0);
}



jQuery('#insertAverage').on('click', function(e){
    e.preventDefault();
    insertAverage();
    canSubmit(0);
});

document.getElementById("calcAverage").onclick = function() {calcAverage()};

function insertAverage() {
    jQuery('#method-calc').children().remove();
    jQuery('#btn-method-calc').children().remove()
    var fila = '<div id="row_'+i+'"><div class="form-row"><div class="col-md-12"><div class="form-group"><label for="">Salario Promedio</label><input type="number" name="average_salary_calc" placeholder="Salario Promedio" min="1" required step=".01" id="average_salary_calc" class="form-control" value="0" onchange="calcAverageInput()"></div></div></div></div>';
    jQuery("#method-calc").append(fila);    
}

canRemove();
canAddRow();
canSubmit(0);

jQuery('#submit').on('click', function(evant){
    event.preventDefault(); 
    if(statusSubmit) {
        Swal.fire({
            title: '¡Enviar!',
            icon: 'info',
            text: '¿Estas seguro que deseas enviar el formulario?',
            showCloseButton: true,
            showCancelButton: true,
            confirmButtonText:
            `<buttom type="submit" name="enviar" id="confirm" onclick='document.getElementById("nws-form-001").submit();'>Envair</buttom>`,
            cancelButtonText:'Cancelar'
        });
    }    
});

jQuery('#confirm').on('click', function(event){
    
});

jQuery(document).ready(function(jQuery) {
});