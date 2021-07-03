<?php 

function nws_form_insert_form() {
?>

<div class="principal contenedor contacto">
    <div class="text-centrado contenido-paginas">
    <form action="" class="reserva-contacto" method="post" id="nws-form-001">
        <div id="nws_form_001">
            <div id="row_1">
                <div class="form-row">
                    <div class="col-md-4">
                        <label for="">Nombre</label>
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Nombre" required class="form-control" required>                    
                        </div>
                    </div>                    
                    <div class="col-md-4">
                        <label for="">Apellido</label>
                        <div class="form-group">                   
                            <input type="text" name="last_name" placeholder="Apellido" required class="form-control" required>
                        </div>
                    </div> 
                    <div class="col-md-4">
                        <label for="">Fecha de nacimiento</label>
                        <div class="form-group">                            
                            <input type="date" name="birthday" placeholder="Fecha de nacimiento" required  value="<?php echo date("2005-01-01");?>" id="birthday" class="form-control" required>                 
                        </div>
                    </div>                    
                </div>

                <div class="form-row">                    
                   <div class="col-md-6">
                        <div class="form-group">                   
                            <input type="email" name="email" placeholder="Correo" required class="form-control" required>
                        </div>
                   </div>
                   <div class="col-md-6">
                        <div class="form-group">
                            <input type="number" name="in_demand_weeks" placeholder="Semanas Cotizadas" required step=".01" id="in_demand_weeks" onchange="timeCalc(1)" class="form-control" min="1">                   
                        </div>
                    </div>                    
                </div>

                <div class="form-row">                    
                   <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-md-10">
                                <label for="">El alta en el IMSS fue antes del 1º de Julio de 1997.</label> 
                            </div>
                            <div class="col-md-2" style="text-align:center">                                              
                                <input type="checkbox" class="form-control" aria-label="Checkbox for following text input" name="up_imss"  id="up_imss" value="true">
                            </div>
                        </div>                        
                   </div>
                   <div class="col-md-6">
                   <div class="form-row">
                            <!--<div class="col-md-10">
                                <label for="">Tienes hijos, o padres o esposa.</label> 
                            </div>
                            <div class="col-md-2"  style="text-align:center">                                              
                                <input type="checkbox" class="form-control" aria-label="Checkbox for following text input" name="have_child" id="have_child" value="true">
                            </div>-->
                            <input type="hidden" name="have_child" value="true">
                        </div>
                    </div>                    
                </div>

                <div class="form-row">                                    
                  <div class="col-md-6">
                    <label for="">Al momento de la edad de pensionarte, ¿cuantos hijos sera menor de edad?</label>                    
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">                                           
                      <input type="number" name="num_child" placeholder="Número de hijos" required id="num_child" class="form-control" value="0" min="0">
                    </div>
                  </div>                    
                </div>

                <div class="form-row">                                    
                  <div class="col-md-12" class="text-center">
                    <label for="">¿Cuentas con el salario promedio?</label>                    
                  </div>                   
                </div>

                <div class="form-row">                                    
                    <div class="col-md-12" class="container-btn text-center">
                        <button type="button" id="insertAverage">Sí</button>
                        <button type="button" id="calcAverage">No</button>
                    </div>                   
                </div>

                

                <!-- Desde este punto se añaden los campos a calcular -->
                <div class="method-calc" id="method-calc"> 
                </div>
                <div class="btn-method-calc" id="btn-method-calc">
                    <div class="form-row container-btn">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-row">
            <div class="col-md-6">
                <div class="g-recaptcha" data-sitekey="<?php echo esc_attr( get_option( 'nws_form_site_key' ) );?>"></div>
            </div>                                   
        </div>       
        <br/>
        <buttom type="buttom" name="enviar" class="button" id="submit">Enviar</buttom>
        
        <input type="hidden" name="oculto" value="1">
        <input type="hidden" name="enviar" value="1">
        <input type="hidden" name="increment_determination"  id="increment_determination" value="0">
        <input type="hidden" name="average_salary_total"  id="average_salary_total" value="0">
    </form>
    </div>
</div>

<?php
}
add_shortcode( 'nws_form', 'nws_form_insert_form' );

?>
