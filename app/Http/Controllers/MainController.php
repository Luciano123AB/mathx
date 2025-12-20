<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MainController extends Controller
{
    public function home(): View {
        return view("home");
    }

    public function generateExercises(Request $request) {
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
        $operations[] = $request->check_sum ? "sum" : "";
        $operations[] = $request->check_subtraction ? "subtraction" : "";
        $operations[] = $request->check_multiplication ? "multiplication" : "";
        $operations[] = $request->check_division ? "division" : "";

        // Get numbers (min and max):
        $min = $request->number_one;
        $max = $request->number_two;

        // Get number of exercises:
        $number_exercises = $request->number_exercises;

        // Generate exercises:
        $exercises = [];
        for ($i = 1; $i <= $number_exercises; $i++) {

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
                    $exercise = "$number01 * $number02 = ";
                    $solution = $number01 * $number02;
                break;

                case "division":
                    $exercise = "$number01 / $number02 = ";
                    $solution = $number01 / $number02;
                break;
            }

            $exercises[] = [
                "exercise_number" => $i,
                "exercise" => $exercise,
                "solution" => "$exercise $solution"
            ];
        }

        dd($exercises);
    }

    public function printExercises() {
        echo "Imprimir exercícios no navegador.";
    }

    public function exportExercises() {
        echo "Exportar exercícios para um arquivo de texto.";
    }
}
