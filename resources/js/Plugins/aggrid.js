import 'ag-grid-community/styles/ag-grid.css'
import 'ag-grid-community/styles/ag-theme-quartz.css'

import { ModuleRegistry } from 'ag-grid-community'
import {
    ClientSideRowModelModule,
    ValidationModule,
} from 'ag-grid-community'

ModuleRegistry.registerModules([
    ClientSideRowModelModule,
    ValidationModule,
])
