%2F%2F%2F AthomeSolution ImportBundle, [ImportService|+ValidatorManager;]<>->[ValidatorManager],[ImportService]-[<<DetectorInterface>>],[<<DetectorInterface>>]<>->[ConfigManager],[<<DetectorInterface>>]<>->[<<FormatManagerInterface>>],[<<FormatManagerInterface>>]^-.-[CsvManager],[CsvManager]<>->[FormatHandlerX],[<<FormatManagerInterface>>]-[DatabaseManager],[ConfigManager]<>-*>[Column],[ConfigManager]<>->[ConfigHandlerX],[ConfigHandlerX]-.-[FormatHandlerX]

    athome_solution_import_bundle:
        map:
            data_column: App\Entity\Columns\DataColumn
            data_text_column: App\Entity\Columns\DataTextColumn
            data_bool_column: App\Entity\Columns\DataBoolColumn
            data_date_column: App\Entity\Columns\DataDateColumn
            data_percent_column: App\Entity\Columns\DataPercentColumn
            data_int_column: App\Entity\Columns\DataIntColumn
            data_float_column: App\Entity\Columns\DataFloatColumn
        source: yaml
        load_default: true
        detector: '@'
        factory: '@'
        format:
            - csv
            - json
            - xml
            - xslx
    user:
        formatter: Formatter.php
        columns:
            name:
                column_name: username
                class: Entity/User
                method: setUsername()
                attribute: ~ 
                validator: MyService
            name:
                column_name: username
                class: Entity/Manager
                method: setUsername()
                attribute: ~ 
                validator: MyService
    excel:
        data_processor: MyExcelProcessor.php
        user:
            name:
                column_name: username
                class: ENtity/User
                method: ~
                attribute: username
                type: string
                
                
![Uml](https://www.yuml.me/5d73f1d4.png)