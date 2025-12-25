<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MainController extends Controller
{
    public function home(): View {
        return view("home");
    }

    public function generateExercises(Request $request): View {
        // Form vadidation:
        $request->validate([
            "check_sum" => "required_without_all:check_subtraction,check_multiplication,check_division",
            "check_subtraction" => "required_without_all:check_sum,check_multiplication,check_division",
            "check_multiplication" => "required_without_all:check_sum,check_subtraction,check_division",
            "check_division" => "required_without_all:check_sum,check_subtraction,check_multiplication",
            "number_one" => "required|integer|min:0|max:999|lt:number_two",
            "number_two" => "required|integer|min:0|max:999",
            "number_exercises" => "required|integer|min:5|max:50"
        ]);

        // Get selected operations:
        $operations = [];
        if ($request->check_sum) { $operations[] = "sum"; }
        if ($request->check_subtraction) { $operations[] = "subtraction"; }
        if ($request->check_multiplication) { $operations[] = "multiplication"; }
        if ($request->check_division) { $operations[] = "division"; }

        // Get numbers (min and max):
        $min = $request->number_one;
        $max = $request->number_two;

        // Get number of exercises:
        $number_exercises = $request->number_exercises;

        // Generate exercises:
        $exercises = [];
        for ($i = 1; $i <= $number_exercises; $i++) {
            $exercises[] = $this->generateExercise($i, $operations, $min, $max);            
        }

        //Place exercises in session:
        $request->session()->put("exercises", $exercises);
        //ou
        session(["exercises" => $exercises]);

        return view("operations", ["exercises" => $exercises]);
    }

    public function printExercises() {
        echo "Imprimir exercícios no navegador.";
    }

    public function exportExercises() {
        echo "Exportar exercícios para um arquivo de texto.";
    }

    private function generateExercise($i, $operations, $min, $max): array {
        
        $operation = $operations[array_rand($operations)];
        $number01 = rand($min, $max);
        $number02 = rand($min, $max);
        $exercise = "";
        $solution = "";

        switch ($operation) {
            case "sum":
                $exercise = "$number01 + $number02 = ";
                $solution = $number01 + $number02;
            break;

            case "subtraction":
                $exercise = "$number01 - $number02 = ";
                $solution = $number01 - $number02;
            break;

            case "multiplication":
                $exercise = "$number01 x $number02 = ";
                $solution = $number01 * $number02;
            break;

            case "division":
                // Avoid division by zero:
                if ($number02 == 0) {
                    $number02 = 1;
                }

                $exercise = "$number01 : $number02 = ";
                $solution = $number01 / $number02;
            break;
        }

        // If $solution is a float number, round it to 2 decimal places:
        if (is_float($solution)) {
            $solution = round($solution, 2);
        }

        return [
            "operation" => $operation,
            "exercise_number" => $i,
            "exercise" => $exercise,
            "solution" => "$exercise $solution"
        ];
    }
}
